#!/usr/local/cpanel/3rdparty/bin/php -q
<?php

// Embed hook attribute information
function describe()
{
    // Configuration for Api2::Cron::add_line hook
    $api2_add_hook = array(
        'blocking' => 1,
        'category' => 'Cpanel',
        'event'    => 'Api2::Email::addforward', // Change to the appropriate event
        'stage'    => 'pre',
        'hook'     => '/usr/local/src/custom/disable_email_forward_hook.php --add_api2', // Change to the actual path of your script
        'exectype' => 'script',
    );

    // Configuration for UAPI::Cron::add_line hook
    $uapi_add_hook = array(
        'blocking' => 1,
        'category' => 'Cpanel',
        'event'    => 'UAPI::Email::add_forwarder', // Change to the appropriate event
        'stage'    => 'pre',
        'hook'     => '/usr/local/src/custom/disable_email_forward_hook.php --add_uapi', // Change to the actual path of your script
        'exectype' => 'script',
    );

    return array($api2_add_hook, $uapi_add_hook);
}

// Process data from STDIN
function get_passed_data()
{
    // Get input from STDIN
    $raw_data = '';
    $stdin_fh = fopen('php://stdin', 'r');
    if (is_resource($stdin_fh)) {
        stream_set_blocking($stdin_fh, 0);
        while (($line = fgets($stdin_fh, 1024)) !== false) {
            $raw_data .= trim($line);
        }
        fclose($stdin_fh);
    }

    // Process and JSON-decode the raw output
    if ($raw_data) {
        $input_data = json_decode($raw_data, true);
    } else {
        $input_data = array('context' => array(), 'data' => array(), 'hook' => array());
    }

    // Return the output
    return $input_data;
}

// Cpanel::UAPI::Email::add_forwarder
// We strongly recommend that you use UAPI::Email::add_forwarder instead of Api2::Email::addforward
function add_uapi($input = array())
{
    // Uncomment the lines below for debugging
    //error_log("add_uapi\n");
    //error_log(print_r($input, true));

    return add($input, 'uapi');
}

// Cpanel::Api2::Email::addforward
// We strongly recommend that you use UAPI::Email::add_forwarder instead of Api2::Email::addforward
function add_api2($input = array())
{
    // Uncomment the lines below for debugging
    //error_log("add_api2\n");
    //error_log(print_r($input, true));

    return add($input, 'api2');
}

// Process input data to determine whether to allow or block the addition of a cron job based on specified criteria
function add($input, $api_type)
{
    // Determine the API function based on the selected type
    $api_function = 'uapi' === $api_type ? 'UAPI::Email::add_forwarder' : 'Api2::Email::addforward';
    $input_context = $input['context'];
    $input_args = $input['data']['args'];
    $email_to = trim($input_args['fwdemail']);
    $action_api = $input_context['event'];
    $action_forward = $input_args['fwdopt'];

    // List of prohibited email domains
    $prohibited = array(
        'hotmail',
        'outlook',
        'live',
        'msn',
        'googlemail',
        'gmail',
        'yahoo',
        'ymail',
        'aol'
    );

    // $result = Set success boolean value
    // 1 — Success
    // 0 — Failure

    // $message = This string is a reason for $result

    // To block the hook event on failure, you must set the blocking value to 1
    // in the describe() method and include BAILOUT in the failure message. If
    // the message does not include BAILOUT, the system will not block the event.

    // Check if the API function matches the expected action API and the action is for forwarding
    if ($api_function === $action_api && 'fwd' === $action_forward) {

        // Check if the provided email address is valid
        if (filter_var($email_to, FILTER_VALIDATE_EMAIL)) {

            // Sanitize the email address
            $sanitized_email_to = filter_var($email_to, FILTER_SANITIZE_EMAIL);

            // Extract the domain from the sanitized email address
            $email_to_domain = array_pop(explode('@', $sanitized_email_to));

             // Extract domain segments without the top-level domain (TLD)
            $domainSegments = explode('.', $email_to_domain);
            
            // Check for common prohibited domains
            $commonDomains = array_intersect($domainSegments, $prohibited);

            if (!empty($commonDomains)) {
                // Block forwarding to prohibited domains
                $result = 0;
                $message = "Forwarding to {$email_to_domain} is not allowed.";
            }
            else {
                // Allow forwarding
                $result = 1;
                $message = "";
            }
            
        } else {
            // Invalid email address
            $result = 0;
            $message = "Invalid email address.";
        }
    } else {
        // Not processing forwarding or action API doesn't match expected API function
        $result = 1;
        $message = "";
    }

    // On error, use:
    // throw new RuntimeException("BAILOUT $message");

    // Return the hook result and message
    return array($result, $message);
}

// Any switches passed to this script
$switches = (count($argv) > 1) ? $argv : array();

// Argument evaluation
if (in_array('--describe', $switches)) {
    // Output hook descriptions in JSON format
    echo json_encode(describe());
    exit;
} elseif (in_array('--add_api2', $switches)) {
    // Add cron job using Api2::Cron::add_line
    $input = get_passed_data();
    list($result, $message) = add_api2($input);
    echo "$result $message";
    exit;
} elseif (in_array('--add_uapi', $switches)) {
    // Add cron job using UAPI::Cron::add_line
    $input = get_passed_data();
    list($result, $message) = add_uapi($input);
    echo "$result $message";
    exit;
} else {
    // Invalid switch provided
    echo '0 custom/disable_email_forward_hook.php needs a valid switch';
    exit(1);
}
