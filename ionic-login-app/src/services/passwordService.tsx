import axios from 'axios';

const api = axios.create({
    baseURL: `https://ionic-login-poc.ddev.site`
})
export function RequestPassword(email: string) {
    return api.post("/forget-password", {"email" : email});
};
export function UpdatePassword(token: string, password: string) {
    return api.post("/update-password", 
        {
            "passwordRequestToken" : token,
            "plainPassword" : password
        });
}

