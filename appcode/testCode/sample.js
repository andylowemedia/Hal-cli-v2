var universe = require('./universe');
console.log(universe.answer()); // prints "It's 42"

var casper = require('casper').create({
    clientScripts: ["jquery.min.js"]
});

var url = 'https://www.huffingtonpost.co.uk/entry/declan-donnelly-saturday-night-takeaway_uk_5ac1de1de4b055e50aceda25?u6&utm_hp_ref=uk-homepage';
//var url = 'http://www.bbc.co.uk/news/uk-england-leeds-43617013';

var json = {
    title       : '',
    subtitle    : '',
    content     : '',
    images      : '',
    links       : [],
    url         : ''
};

casper.on('loaded.parseTitle', function() {
    this.echo(this.getCurrentUrl());
    json.title = this.evaluate(function() {
        return jQuery('h1').html();
    });
    
});

casper.on('loaded.parseSubtitle', function() {
    var element = 'h2';
    json.subtitle = this.evaluate(function(element) {
        return jQuery(element).html();
    }, element);
    
});

casper.on('loaded.parseBody', function() {

    json.content = this.evaluate(function() {
        var paragraph = '';
        jQuery('p').each(function() {
            paragraph += '<p>' + jQuery(this).html() + '</p>';
        });
        return paragraph;
    });
    
});

casper.start(url, function() {
    this.emit('loaded.parseTitle');
    this.emit('loaded.parseSubtitle');
    this.emit('loaded.parseBody');
    
    this.echo(JSON.stringify(json));
    
//    this.echo(this.getCurrentUrl());
//    
//    this.echo(this.evaluate(function() {
//        var something = function() {
//            jQuery('p').remove()
//        };
//        something();
//        
//        json.url = location.href;
//        json.title = jQuery('h1').html();
//        json.subtitle = jQuery('h2').html();
//        
//        jQuery('img').each(function() {
//            json.images += jQuery(this).attr('src');
//        });
////        jQuery('p').each(function() {
////            json.content += '<p>' + jQuery(this).html() + '</p>';
////        });
////        
//        jQuery('a').each(function() {
//            var link = jQuery(this).attr('href');
//            if (json.links.indexOf(link) === -1) {
//                json.links.push(link);
//            }
//        });
//        
//        return JSON.stringify(json);
//    }));
});

//casper.thenOpen('http://phantomjs.org', function() {
//    this.echo('Second Page: ' + this.getTitle());
//});

casper.run();
