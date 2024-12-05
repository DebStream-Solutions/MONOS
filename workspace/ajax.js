$(document).ready(function () {
    // Function to fetch SNMP data
    function fetchSNMPData() {
        $.ajax({
            url: 'ajax-snmp.php', // Path to your PHP script
            method: 'POST',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('.generated').text(response.data);
                } else {
                    $('.generated').text('Error: ' + response.message);
                }
            },
            error: function () {
                $('.generated').text('Error: Failed to fetch data');
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