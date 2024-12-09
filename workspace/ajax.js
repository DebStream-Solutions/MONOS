$(document).ready(function () {
    // Function to fetch SNMP data
    function fetchSNMPData() {
        $.ajax({
            url: '../ajax-snmp.php', // Path to your PHP script
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
    fetchSNMPData();
});