<?php
ini_set('display_errors', 'On');
include 'connection.php';

/* user2 is a chemist. */
$user2 = 'user2';
$user2_classcodes = array(
  '68',     // Chemicals and chemical products
  '91',     // Fuels, lubricants, oil and waxes
);
$user2_naics = array(
    '339930',   // Doll, toy, and game manufacturing
    '541711',   // Nucleic acid chemistry research and experimental development laboratories
    '336390',   // Other motor vehicle parts manufacturing
    '314994',   // Rope, cordage, twine, tire cord, and tire fabric mills
    '334413',   // Semiconductor and related device manufacturing
    '335999',   // All other miscellaneous electrical equipment and component manufacturing
    '334515',   // Instrument manufacturing for measuring and testing electricity and electrical signals
    '335122',   // Commercial, industrial, and institutional electric lighting fixture manufacturing
    '335312',   // Motor and generator manufacturing
    '335911',   // Storage battery manufacturing
    '454310',   // Fuel dealers
    '213115',   // Support Activities for Nonmetallic Minerals (except Fuels) Mining
    '221112',   // Fossil Fuel Electric Power Generation
    '238220',   // Plumbing, Heating, and Air-Conditioning Contractors
    '314994',   // Rope, Cordage, Twine, Tire Cord, and Tire Fabric Mills
    '321999',   // All Other Miscellaneous Wood Product Manufacturing
    '324110',   // Petroleum Refineries
    '324199',   // All Other Petroleum and Coal Products Manufacturing
    '325180',   // Other Basic Inorganic Chemical Manufacturing
    '325199',   // All Other Basic Organic Chemical Manufacturing
    '326299',   // All Other Rubber Product Manufacturing
    '334413',   // Semiconductor and Related Device Manufacturing
    '334514',   // Totalizing Fluid Meter and Counting Device Manufacturing
    '334519',   // Other Measuring and Controlling Device Manufacturing
    '335311',   // Power, Distribution, and Specialty Transformer Manufacturing
    '336211',   // Motor Vehicle Body Manufacturing
    '336310',   // Motor Vehicle Gasoline Engine and Engine Parts Manufacturing
    '336320',   // Motor Vehicle Electrical and Electronic Equipment Manufacturing
    '336390',   // Other Motor Vehicle Parts Manufacturing
    '336413',   // Other Aircraft Parts and Auxiliary Equipment Manufacturing
    '423120',   // Motor Vehicle Supplies and New Parts Merchant Wholesalers
    '423520',   // Coal and Other Mineral and Ore Merchant Wholesalers
    '423720',   // Plumbing and Heating Equipment and Supplies (Hydronics) Merchant Wholesalers
    '424690',   // Other Chemical and Allied Products Merchant Wholesalers
    '424710',   // Petroleum Bulk Stations and Terminals
    '424720',   // Petroleum and Petroleum Products Merchant Wholesalers (except Bulk Stations and Terminals)
    '454310',   // Fuel Dealers
    '488190',   // Other Support Activities for Air Transportation
);

$query = "SELECT * FROM ctr_funding_base WHERE ";
foreach($user2_classcodes as $code){
    $query_list[] = "(interests LIKE BINARY '%cc:%" . $code . "%' AND interests NOT LIKE BINARY '%;naics:%" . $code . "%')";
}
foreach($user2_naics as $code){
    $query_list[] = "(interests LIKE BINARY '%naics:" . $code . "%')";
}
$query = $query . implode(" OR ", $query_list);

$result = $mysqli->query($query);
while($row = $result->fetch_assoc()) {
    $query = "INSERT INTO ctr_user_fund_link SET email = '" . $user2 . "', fund_id = '" . $row['id'] . "'";
    
    if(!$mysqli->query($query)) {
        echo "Query failed: " . $mysqli->error . '<br>';
    } else {
        echo "email " . $user2 . " has been associated with opportunity ". $row['id'] . " .<br>";
    }
}

?>

