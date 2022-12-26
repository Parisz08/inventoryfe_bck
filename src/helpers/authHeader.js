import axios from 'axios'

export default {
    load() {
        let token = localStorage.getItem('token');
        if (token) {
            axios.defaults.headers.common['Authorization'] = 'Bearer '+token;
        }
    },
    set(token) {
        localStorage.setItem('token', token);
        axios.defaults.headers.common['Authorization'] = 'Bearer '+token;
    },
    unset() {
        localStorage.removeItem('token');
        delete axios.defaults.headers.common['Authorization'];
    }
}