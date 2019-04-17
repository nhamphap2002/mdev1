
/*
 * Created on : Mar 26, 2019, 3:51:23 PM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 */
var $state = {
    'AU': {
        "Western Australia": "WA",
        "South Australia": "SA",
        "Northern Territory": "NT",
        "Victoria": "VIC",
        "Tasmania": "TAS",
        "Queensland": "QLD",
        "New South Wales": "NSW",
        "Australian Capital Territory": "ACT"
    },
    'CA': {
        "Alberta": "AB",
        "British Columbia": "BC",
        "Manitoba": "MB",
        "New Brunswick": "NB",
        "Northwest Territories": "NT",
        "Nova Scotia": "NS",
        "Nunavut": "NU",
        "Ontario": "ON",
        "Prince Edward Island": "PE",
        "Quebec": "QC",
        "Saskatchewan": "SK",
        "Yukon": "YT",
        "Newfoundland and Labrador": "NL"

    },
    'US': {
        "Arkansas": "AR",
        "District of Columbia": "DC",
        "Delaware": "DE",
        "Florida": "FL",
        "Georgia": "GA",
        "Kansas": "KS",
        "Louisiana": "LA",
        "Maryland": "MD",
        "Missouri": "MO",
        "Mississippi": "MS",
        "North Carolina": "NC",
        "Oklahoma": "OK",
        "South Carolina": "SC",
        "Tennessee": "TN",
        "Texas": "TX",
        "West Virginia": "WV",
        "Alabama": "AL",
        "Connecticut": "CT",
        "Iowa": "IA",
        "Illinois": "IL",
        "Indiana": "IN",
        "Maine": "ME",
        "Michigan": "MI",
        "Minnesota": "MN",
        "Nebraska": "NE",
        "New Hampshire": "NH",
        "New Jersey": "NJ",
        "New York": "NY",
        "Ohio": "OH",
        "Rhode Island": "RI",
        "Vermont": "VT",
        "Wisconsin": "WI",
        "California": "CA",
        "Colorado": "CO",
        "New Mexico": "NM",
        "Nevada": "NV",
        "Utah": "UT",
        "Arizona": "AZ",
        "Idaho": "ID",
        "Montana": "MT",
        "North Dakota": "ND",
        "Oregon": "OR",
        "South Dakota": "SD",
        "Washington": "WA",
        "Wyoming": "WY",
        "Hawaii": "HI",
        "Alaska": "AK",
        "Kentucky": "KY",
        "Massachusetts": "MA",
        "Pennsylvania": "PA",
        "Virginia": "VA",
        "American Samoa": "AS",
        "Virgin Islands": "VI"
    }
}

jQuery(document).ready(function ($) {

    jQuery.validator.addMethod("card_code", function (value, element) {
        var $ischeck = false;
        if (value.length == 3) {
            $ischeck = true;
        }
        // allow any non-whitespace characters as the host part
        return this.optional(element) || $ischeck;
    }, 'Please enter a valid credit card verification number.');

    jQuery.validator.addMethod("validateccnumber", function (value, element) {
        // allow any non-whitespace characters as the host part
        return this.optional(element) || value.match('^5[1-5][0-9]{14}$');
    }, 'Credit card number does not match credit card type MasterCard');
    jQuery.validator.addMethod("validate-cc-exp", function (value, element) {
        var ccExpMonth = value;
        var ccExpYear = $('#ccsave_expiration_yr').val();
        var currentTime = new Date();
        var currentMonth = currentTime.getMonth() + 1;
        var currentYear = currentTime.getFullYear();
        var $result = false;
        if (ccExpMonth < currentMonth && ccExpYear == currentYear) {
            $result = false;
        } else {
            $result = true;
        }
        return this.optional(element) || $result;
    }, 'Incorrect credit card expiration date.');
    $("#co-payment-form").validate();
    $('#pay_country').change(function () {
        var $pay_country = $.trim($(this).val()), $html = '';
        $html = '';
        if ($.trim($pay_country) !== '') {
            if (typeof $state[$pay_country] != 'undefined') {
                $html = '<select name="state" class="form-control" required id="pay_state">';
                for (var i in $state[$pay_country]) {
                    $html += '<option value="' + $state[$pay_country][i] + '">' + i + '</option>';
                }
                $html += '</select>';
            } else {
                $html = '<input type="text" class="form-control" required name="state" value=""/>';
            }
        } else {
            $html = '<input type="text" class="form-control" required name="state" value=""/>';
        }
        $('.pay_state').html($html);
    })
})


