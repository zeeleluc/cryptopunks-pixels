<?php
/**
 * Created by Luc from FamilyNFTs(.nl)
 *
 * Techniques in this repository are used when creating the following NFT collections:
 *  - RipplePunks & RipplePunks Rewind on XRPL
 *  - LoadingPunks &
 *    PipingPunks &
 *    OpepePunks &
 *    ShapedPunks on Ethereum
 *  - BaseAliens on Base
 *
 * For this repository certain techniques are wrapped in once class purely for demonstration.
 * You can use any parts of the code as you wish.
 */

include_once 'preload.php';
include_once 'autoloader.php';
include_once 'utilities.php';

if (is_cli()) {

    try {
        if (!isset($argv[0]) && !isset($argv[1])) {
            throw new Exception('Missing required parameters');
        }

        // validate action
        $action = camelize($argv[1]);
        if (!method_exists(Punk::class, $action)) {
            throw new Exception('Method `' . $action . '` does not exists');
        }

        // validate id
        $id = $argv[2] ?? null;
        if ($id && ($id < 0 || $id > 9999)) {
            throw new Exception('ID #' . $id . ' does not exists');
        }

        // show some info in the terminal
        echo 'Running action `' . $action . '`' . ($id ? ' for #' . $id : '') . PHP_EOL;

        // perform the action
        $punk = new Punk($id);
        $result = $punk->{$action}();

        if ($result) {
            echo '-------' . PHP_EOL;
            if (is_array($result)) {
                $json = json_encode($result, JSON_PRETTY_PRINT);
                print_r($json);
            }
            echo PHP_EOL;
        }

    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL;
    }
}
