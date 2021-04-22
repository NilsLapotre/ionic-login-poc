import axios from 'axios';

const api = axios.create({
    baseURL: `http://ionic-login-poc.ddev.site`
})
export function login(loginData: any) {
    return api.post("/login", loginData);
};

