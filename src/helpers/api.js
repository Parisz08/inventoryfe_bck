import router from '@/router'

export default (context, service) => {
    let api = {
        default: {
            // eslint-disable-next-line no-unused-vars
            onSuccess(response) {
                
            },
            // eslint-disable-next-line no-unused-vars
            onError(error) {                             

            },
            onFinish() {
                
            },
        },
        onSuccess(handler) {            
            api.successHandling = handler 
            return api; 
        },
        onError(handler) {            
            api.errorHandling = handler  
            return api; 
        },
        onFinish(handler) {
            api.finishHandling = handler
            return api; 
        },
        successHandling: function(response) {
            api.default.onSuccess(response)
        },
        errorHandling: function(error) {
            api.default.onError(error)
        },
        finishHandling: function() {
            api.default.onFinish()
        },
        call: function() {
            service.then(response => {                
                api.successHandling(response)    
            }).catch(error => {
                if (error.response.status == 401) {
                    localStorage.removeItem('auth');
                    localStorage.setItem('authenticated', false) 
                    router.push('/login');
                }   
                api.errorHandling(error)    
            }).then(() => {
                api.finishHandling()
            })
        }
    }    
    return api
}