import axios from "axios";

const httpClient = axios.create({
    // TODO get baseurl from somewhere!
    baseUrl: "http://localhost:8081",
    headers: {
        "Content-Type": "application/json",
        timeout: 3000, // 3 second
    }
});

export default httpClient;
