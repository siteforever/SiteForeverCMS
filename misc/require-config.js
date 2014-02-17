requirejs.config({
    'shim': {
        'jquery': {'exports': '$'},
        'underscore': {'exports': '_'},
        'backbone': {'exports': 'Backbone', 'deps': ['underscore', 'jquery']}
    }
});
define("jquery", function () {
    return jQuery;
});
define("underscore", function () {
    return _;
});
define("backbone", function () {
    return Backbone;
});
