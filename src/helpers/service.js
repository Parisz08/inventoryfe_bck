import axios from 'axios'
import config from '@/configs/config'
import authHeader from '@/helpers/authHeader'

export default () => {
    authHeader.load();    
    return axios.create({
        baseURL: config.apiUrl
    });
}