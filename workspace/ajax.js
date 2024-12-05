$(document).ready(function () {
    // Function to fetch SNMP data
    function fetchSNMPData() {
        $.ajax({
            url: 'ajax-snmp.php', // Path to your PHP script
            method: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('.generated').text(response.data);
                } else {
                    $('.generated').text('Error: ' + response.message);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                console.error('Response Text:', jqXHR.responseText);
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