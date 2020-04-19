FROM cemalklc/base-phpfpm-nginx:0.1

# add bitbucket and github to known hosts for ssh needs
WORKDIR /root/.ssh
RUN chmod 0600 /root/.ssh \
    && ssh-keyscan -t rsa bitbucket.org >> known_hosts \
    && ssh-keyscan -t rsa github.com >> known_hosts

# Install yarn itself
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
  && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
  && apt-get update \
  && apt-get install yarn -y \
  && yarn install \
  && rm -r /var/lib/apt/lists/*

# Install dependencies for both composer and yarn
WORKDIR /var/www/app
COPY ./composer.json ./composer.lock* ./package.json ./yarn.lock ./
ENV COMPOSER_VENDOR_DIR=/var/www/app/vendor
RUN composer install -o --no-interaction --no-scripts
RUN yarn install

# Custom php-fpm pool settings, these get written at entrypoint startup
ENV FPM_PM_MAX_CHILDREN=20 \
    FPM_PM_START_SERVERS=2 \
    FPM_PM_MIN_SPARE_SERVERS=1 \
    FPM_PM_MAX_SPARE_SERVERS=3

ENV DB_CONNECTION=mysql \
    DB_HOST=mysql \
    DB_PORT=3306 \
    DB_DATABASE=dummy \
    DB_USERNAME=dummy \
    # TODO use docker secrets
    DB_PASSWORD=secret \
    DB_ROOT_PASSWORD=secret

# Entry point files
COPY docker/docker-php-* /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-php-entrypoint
RUN dos2unix /usr/local/bin/docker-php-entrypoint-dev

# copy nginx site config for the app
COPY ./docker/nginx-site.conf /etc/nginx/conf.d/default.conf

# copy in app code as late as possible, as it changes the most
WORKDIR /var/www/app
COPY . .
RUN chown www-data:www-data /var/www/app
RUN composer dump-autoload -o
RUN yarn build

# be sure nginx is properly passing to php-fpm and fpm is responding
HEALTHCHECK --interval=5s --timeout=3s \
  CMD curl -f http://localhost/ping || exit 1

WORKDIR /var/www/app/public
EXPOSE 80 443 9000 9001

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
