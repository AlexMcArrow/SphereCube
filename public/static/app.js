axios.defaults.baseURL = '//' + DOMAIN;

function RPCCALL(method, data, res) {
    axios.post('/api', {
            id: new Date().getTime(),
            jsonrpc: '2.0',
            method: method,
            params: data
        })
        .then(function(response) {
            console.log(response);
            res(response.data.result);
        })
        .catch(function(error) {
            // TODO: show user-error
            console.log(error);
        });

}