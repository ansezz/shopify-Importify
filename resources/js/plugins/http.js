import axios from 'axios'
import {API_URL} from '../config'


export const http = axios.create({
    baseURL: API_URL,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
})

// Add a request interceptor
http.interceptors.request.use(function (config) {
    // Do something before request is sent
    config.data.shop = window.shop
    return config;
}, function (error) {
    // Do something with request error
    return Promise.reject(error);
});

export default http
