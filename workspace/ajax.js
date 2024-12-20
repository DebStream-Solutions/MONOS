$(document).ready(function () {
    function GET(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param); // Returns the value of the parameter or null if not found
    }

    function GET_ALL() {
        const urlParams = new URLSearchParams(window.location.search);
        const params = {};
        urlParams.forEach((value, key) => {
            params[key] = value;
        });
        return params;
    }


    // -- Ajax --------

    function ajax(path, time) {
        $.ajax({
            url: path, // Path to your PHP script
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {

                    console.log("Data refresh");

                    if (response.data && typeof response.data === 'object') {
                        Object.entries(response.data).forEach(([key, value]) => {
                            let element = `#${key}`;
                            $(element).html(value);
                        });
                    } else {
                        console.log('Invalid data format:', response.data);
                    }

                } else {
                    $('.generated').html('<div style="margin: auto; text-align: center;">Error: ' + response.message + '</div>');
                }
            },
            error: function () {
                $('.generated').html('<div style="margin: auto; text-align: center;">Error: Failed to fetch data</div>');
            },
            complete: function () {
                // Re-run the function after X seconds
                timeout = time * 1000;

                setTimeout(() => {
                    ajax(path, time);
                }, timeout);
            }
        });
    }



    // -- Ajax Call ---

    function ajaxProcess(timeouts, func, detail) {

        timeouts.forEach(time => {
            if (detail) {
                let url = '../ajax-'+time+'.php?func='+func;
            } else {
                let url = 'ajax-'+time+'.php?func='+func;
            }
            
            
            ajax(url, time);
        });
    }


    function fetchData() {
        let get = GET("profile");
        console.log(get);
        if (get) {
            let detail = false;
            let deviceGet = GET("device");
            if (deviceGet) {
                detail = true;
            }
            
            let timeouts = [5, 10];
            let func = "device";
            ajaxProcess(timeouts, func, detail);
        }
    }

    fetchData();
}


































/*
    // Function to fetch SNMP data
    function GET(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param); // Returns the value of the parameter or null if not found
    }

    function GET_ALL() {
        const urlParams = new URLSearchParams(window.location.search);
        const params = {};
        urlParams.forEach((value, key) => {
            params[key] = value;
        });
        return params;
    }

    let func = GET("func");
    func = func ? func : "";

    let gets = func;


    function fetchSNMPData(GET) {
        $.ajax({
            url: '../ajax-snmp.php?func=' + GET, // Path to your PHP script
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {

                    console.log("Data refresh");

                    if (response.data && typeof response.data === 'object') {
                        Object.entries(response.data).forEach(([key, value]) => {
                            let element = `#${key}`;
                            $(element).html(value);
                        });
                    } else {
                        console.log('Invalid data format:', response.data);
                    }

                } else {
                    $('.generated').html('<div style="margin: auto; text-align: center;">Error: ' + response.message + '</div>');
                }
            },
            error: function () {
                $('.generated').text('<div style="margin: auto; text-align: center;">Error: Failed to fetch data</div>');
            },
            complete: function () {
                // Re-run the function after 5 seconds
                setTimeout(fetchSNMPData, 5000);
            }
        });
    }

    // Initial fetch
    fetchSNMPData(gets);
}
    */
);