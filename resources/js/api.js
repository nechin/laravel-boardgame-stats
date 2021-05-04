import axios from 'axios';

export default {
    plays: {
        get(userName) {
            return axios.get(
                `api/v1/plays/${userName}`
            )
        }
    }
}
