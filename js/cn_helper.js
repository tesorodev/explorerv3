// Copyright (c) 2018, NERVA
// Copyright (c) 2014-2017, MyMonero.com

// Based on cn_util.js
// Original Author: Lucas Jones
// Modified to remove jQuery dep and support modular inclusion of deps by Paul Shapiro (2016)
// Modified by luigi1111 2017

var HASH_STATE_BYTES = 200;
var HASH_SIZE = 32;
var ADDRESS_CHECKSUM_SIZE = 4;
var INTEGRATED_ID_SIZE = 8;
var ENCRYPTED_PAYMENT_ID_TAIL = 141;
var CRYPTONOTE_PUBLIC_ADDRESS_BASE58_PREFIX = 0x3800;
var CRYPTONOTE_PUBLIC_INTEGRATED_ADDRESS_BASE58_PREFIX = 0x7081;
var CRYPTONOTE_PUBLIC_SUBADDRESS_BASE58_PREFIX = 0x1080;

function encodeVarint(i)
{
    i = new JSBigInt(i);
    var out = '';
    // While i >= b10000000
    while (i.compare(0x80) >= 0) {
        // out.append i & b01111111 | b10000000
        out += ("0" + ((i.lowVal() & 0x7f) | 0x80).toString(16)).slice(-2);
        i = i.divide(new JSBigInt(2).pow(7));
    }
    out += ("0" + i.toJSValue().toString(16)).slice(-2);
    return out;
}

function valiHex(hex)
{
    var exp = new RegExp("[0-9a-fA-F]{" + hex.length + "}");
    return exp.test(hex);
}

function hexToBin(hex)
{
    if (hex.length % 2 !== 0) throw "Hex string has invalid length!";
    var res = new Uint8Array(hex.length / 2);
    for (var i = 0; i < hex.length / 2; ++i)
        res[i] = parseInt(hex.slice(i * 2, i * 2 + 2), 16);
    
    return res;
}

function cnFastHash(input, inlen)
{
    if (input.length % 2 !== 0 || !this.valid_hex(input))
        throw "Input invalid";

    var bin = hexToBin(input);
    var result = keccak_256(bin)
    return result;
}

const address_type = {
    unknown: "Unknown",
    standard: "Standard",
    integrated: "Integrated",
    subaddress: "Subaddress"
}

function getAddressType(address)
{
    var dec = cnBase58.decode(address);
    var expectedPrefix;
    var prefix;

    //Check if it is a standard address
    expectedPrefix = encodeVarint(CRYPTONOTE_PUBLIC_ADDRESS_BASE58_PREFIX);
    prefix = dec.slice(0, expectedPrefix.length);

    if (prefix == expectedPrefix)
        return {
            address_type: address_type.standard,
            prefix: prefix,
            prefix_length: expectedPrefix.length
        };
    
    //Check integrated address
    expectedPrefix = encodeVarint(CRYPTONOTE_PUBLIC_INTEGRATED_ADDRESS_BASE58_PREFIX);
    prefix = dec.slice(0, expectedPrefix.length);
    
    if (prefix == expectedPrefix)
        return {
            address_type: address_type.integrated,
            prefix: prefix,
            prefix_length: expectedPrefix.length
        };

    //check subaddress
    expectedPrefix = encodeVarint(CRYPTONOTE_PUBLIC_SUBADDRESS_BASE58_PREFIX);
    prefix = dec.slice(0, expectedPrefix.length);
    
    if (prefix == expectedPrefix)
        return {
            address_type: address_type.subaddress,
            prefix: prefix,
            prefix_length: expectedPrefix.length
        };

    return {
        address_type: address_type.unknown,
        prefix: 0,
        prefix_length: 0
    };
}

function decodeAddress(address)
{
    var at = getAddressType(address);

    if (at.address_type == address_type.unknown)
        throw "Could not determine the type of address";

    var dec = cnBase58.decode(address).slice(at.prefix_length);

    var spend = dec.slice(0, 64);
    var view = dec.slice(64, 128);

    if (at.address_type == address_type.integrated)
    {
        var intPaymentId = dec.slice(128, 128 + (INTEGRATED_ID_SIZE * 2));
        var checksum = dec.slice(128 + (INTEGRATED_ID_SIZE * 2), 128 + (INTEGRATED_ID_SIZE * 2) + (ADDRESS_CHECKSUM_SIZE * 2));
        var expectedChecksum = cnFastHash(at.prefix + spend + view + intPaymentId).slice(0, ADDRESS_CHECKSUM_SIZE * 2);
    }
    else {
        // if its not regular address, nor integrated, than it must be subaddress
        var checksum = dec.slice(128, 128 + (ADDRESS_CHECKSUM_SIZE * 2));
        var expectedChecksum = cnFastHash(at.prefix + spend + view).slice(0, ADDRESS_CHECKSUM_SIZE * 2);
    }

    if (checksum !== expectedChecksum)
        throw "Invalid checksum";

    if (intPaymentId){
        return {
            type: at,
            spend: spend,
            view: view,
            intPaymentId: intPaymentId
        };
    }
    else {
        return {
            type: at,
            spend: spend,
            view: view
        };
    }
}