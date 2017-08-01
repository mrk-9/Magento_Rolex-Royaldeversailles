// checking if IE: this variable will be understood by IE: isIE = !false
isIE = /*@cc_on!@*/false;

Control.Slider.prototype.setDisabled = function()
{
    this.disabled = true;

    if (!isIE)
    {
        this.track.parentNode.className = this.track.parentNode.className + ' disabled';
    }
};

function fme_layered_hide_products()
{ 
    var items = $('fme_filters_list').select('a', 'input');
    n = items.length;
    for (i = 0; i < n; ++i) {

        items[i].addClassName('fme_layered_disabled');
    }

    if (typeof (fme_slider) != 'undefined')
        fme_slider.setDisabled();

    var divs = $$('div.fme_loading_filters');
    for (var i = 0; i < divs.length; ++i)
        divs[i].show();
}

function fme_layered_show_products(transport)
{ 
    
    var resp = {};
    if (transport && transport.responseText) {
        try {
            resp = eval('(' + transport.responseText + ')');
        }
        catch (e) {
            resp = {};
        }
    }
    var $j = jQuery.noConflict();
    $j('.page-title').html('<h1>'+resp.category+'</h1>'); 
 
    if (resp.products) {

        var ajaxUrl = $('fme_layered_ajax').value;

        if ($('fme_layered_container') == undefined) {

            var c = $$('.col-main')[0];// alert(c.hasChildNodes());
            if (c.hasChildNodes()) {
                while (c.childNodes.length > 2) {
                    c.removeChild(c.lastChild);
                }
            }

            var div = document.createElement('div');
            div.setAttribute('id', 'fme_layered_container');
            $$('.col-main')[0].appendChild(div);

        }
        
        var el = $('fme_layered_container');
             
        el.update(resp.products.gsub(ajaxUrl, $('fme_layered_url').value));
        el.innerHTML; 
        catalog_toolbar_init();

        $('catalog-filters').update(
                resp.layer.gsub(
                        ajaxUrl,
                        $('fme_layered_url').value
                        )
                );

        $('fme_layered_ajax').value = ajaxUrl;
    }

    var items = $('fme_filters_list').select('a', 'input'); 
    n = items.length;
    for (i = 0; i < n; ++i) {
        items[i].removeClassName('fme_layered_disabled');
    }
    if (typeof (fme_slider) != 'undefined')
        fme_slider.setEnabled();
}

function fme_layered_add_params(k, v, isSingleVal)
{ 
    var el = $('fme_layered_params');
    var params = el.value.parseQuery();

    var strVal = params[k];
    if (typeof strVal == 'undefined' || !strVal.length) {
        params[k] = v;
        
    }
    else if ('clear' == v) {
        params[k] = 'clear';
    }
    else { 
        if (k == 'price')
        {  
            var values = strVal.split('-');
               
            
        }
        else{ 
            var values = strVal.split('-');
        }

        if (-1 == values.indexOf(v)) { 
            if (isSingleVal){ 
                values = [v];
            }
            else{
                values.push(v);
            }
        }
        else {
            values = values.without(v);
        }

        params[k] = values.join('-');
    }
    
    el.value = Object.toQueryString(params).gsub('%2B', '+');
}



function fme_layered_make_request()
{
    fme_layered_hide_products();

    var params = $('fme_layered_params').value.parseQuery();

    if (!params['dir'])
    {
        $('fme_layered_params').value += '&dir=' + 'desc';
    }

    new Ajax.Request(
            $('fme_layered_ajax').value + '?' + $('fme_layered_params').value,
            {
                method: 'get',
                onSuccess: fme_layered_show_products
            }
    );
}


function fme_layered_update_links(evt, className, isSingleVal)
{ 
    var link = Event.findElement(evt, 'A'),
            sel = className + '-selected';

    if (link.hasClassName(sel))
        link.removeClassName(sel);
    else
        link.addClassName(sel);

    //only one  price-range can be selected
    if (isSingleVal) {
        var items = $('fme_filters_list').getElementsByClassName(className);
        var i, n = items.length;
        for (i = 0; i < n; ++i) {
            if (items[i].hasClassName(sel) && items[i].id != link.id)
                items[i].removeClassName(sel);
        }
    }

    fme_layered_add_params(link.id.split('-')[0], link.id.split('-')[1], isSingleVal);

    fme_layered_make_request();

    Event.stop(evt);
}


function fme_layered_attribute_listener(evt)
{
    fme_layered_add_params('p', 1, 1);
    fme_layered_update_links(evt, 'fme_layered_attribute', 0);
}


function fme_layered_price_listener(evt)
{
    fme_layered_add_params('p', 1, 1);
    fme_layered_update_links(evt, 'fme_layered_price', 1);
}

function fme_layered_clear_listener(evt)
{
    var link = Event.findElement(evt, 'A'),
            varName = link.id.split('-')[0];

    fme_layered_add_params('p', 1, 1);
    fme_layered_add_params(varName, 'clear', 1);

    if ('price' == varName) {
        var from = $('adj-nav-price-from'),
                to = $('adj-nav-price-to');

        if (Object.isElement(from)) {
            from.value = from.name;
            to.value = to.name;
        }
    }

    fme_layered_make_request();

    Event.stop(evt);
}


function roundPrice(num) {
    num = parseFloat(num);
    if (isNaN(num))
        num = 0;

    return Math.round(num);
}

function fme_layered_category_listener(evt) {
    var link = Event.findElement(evt, 'A');
    var catId = link.id.split('-')[1];

    var reg = /cat-/;
    if (reg.test(link.id)) { //is search
        fme_layered_add_params('cat', catId, 1);
        fme_layered_add_params('p', 1, 1); 
        fme_layered_make_request();
        Event.stop(evt);
    }
    //do not stop event
}

function catalog_toolbar_listener(evt) {
    catalog_toolbar_make_request(Event.findElement(evt, 'A').href);
    Event.stop(evt);
}

function catalog_toolbar_make_request(href)
{
    var pos = href.indexOf('?');
    if (pos > -1) {
        $('fme_layered_params').value = href.substring(pos + 1, href.length);
    }
    fme_layered_make_request();
}


function catalog_toolbar_init()
{
    var items = $('fme_layered_container').select('.pages a', '.view-mode a', '.sort-by a');
    var i, n = items.length;
    for (i = 0; i < n; ++i) {
        Event.observe(items[i], 'click', catalog_toolbar_listener);
    }
}

function fme_layered_dt_listener(evt) {
    var e = Event.findElement(evt, 'DT');
    e.nextSiblings()[0].toggle();
    e.toggleClassName('fme_layered_dt_selected');
}

function fme_layered_clearall_listener(evt)
{
    var params = $('fme_layered_params').value.parseQuery();
    $('fme_layered_params').value = 'clearall=true';
    if (params['q'])
    {
        $('fme_layered_params').value += '&q=' + params['q'];
    }
    fme_layered_make_request();
    Event.stop(evt);
}

function price_input_listener(evt) {
    if (evt.type == 'keypress' && 13 != evt.keyCode)
        return;

    if (evt.type == 'keypress') {
        var inpObj = Event.findElement(evt, 'INPUT');
    } else {
        var inpObj = Event.findElement(evt, 'BUTTON');
    }

    var sKey = inpObj.id.split('---')[1];
    var numFrom = roundPrice($('price_range_from---' + sKey).value),
            numTo = roundPrice($('price_range_to---' + sKey).value);

    if ((numFrom < 0.01 && numTo < 0.01) || numFrom < 0 || numTo < 0)
        return;

    fme_layered_add_params('p', 1, 1);
    fme_layered_add_params(sKey, numFrom + ',' + numTo, true);
    fme_layered_make_request();
}

function fme_layered_init()
{ 
    var items, i, j, n,
            classes = ['category', 'attribute', 'icon', 'price', 'clear', 'dt', 'clearall'];

    for (j = 0; j < classes.length; ++j) {
        items = $('fme_filters_list').select('.fme_layered_' + classes[j]);
        n = items.length;
        for (i = 0; i < n; ++i) {
            Event.observe(items[i], 'click', eval('fme_layered_' + classes[j] + '_listener'));
        }
    }

    items = $('fme_filters_list').select('.price-input');
    n = items.length;
    var btn = $('price_button_go');
    for (i = 0; i < n; ++i)
    {
        btn = $('price_button_go---' + items[i].value);
        if (Object.isElement(btn)) {
            Event.observe(btn, 'click', price_input_listener);
            Event.observe($('price_range_from---' + items[i].value), 'keypress', price_input_listener);
            Event.observe($('price_range_to---' + items[i].value), 'keypress', price_input_listener);
        }
    }
// finish new fix code    
}

function create_price_slider(width, from, to, min_price, max_price, sKey)
{ 
    var price_slider = $('fme_layered_price_slider' + sKey);

    return new Control.Slider(price_slider.select('.handle'), price_slider, {
        range: $R(0, width),
        sliderValue: [from, to],
        restricted: true,
        onChange: function(values) {
            var f = calculateSliderPrice(width, from, to, min_price, max_price, values[0]),
                t = calculateSliderPrice(width, from, to, min_price, max_price, values[1]);

            fme_layered_add_params(sKey, f + ',' + t, true);

            $('price_range_from' + sKey).update(f);
            $('price_range_to' + sKey).update(t);

            fme_layered_make_request();
        },
        onSlide: function(values) {
            $('price_range_from' + sKey).update(calculateSliderPrice(width, from, to, min_price, max_price, values[0]));
            $('price_range_to' + sKey).update(calculateSliderPrice(width, from, to, min_price, max_price, values[1]));
        }
    });
}

function calculateSliderPrice(width, from, to, min_price, max_price, value)
{
    var calculated = roundPrice(((max_price - min_price) * value / width) + min_price);

    return calculated;
}
