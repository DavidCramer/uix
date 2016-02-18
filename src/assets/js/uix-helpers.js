
  Handlebars.registerHelper("even", function(options) {
    var intval = options.data.index / 2;
    if( intval === Math.ceil( intval ) ){
      return options.fn(this);
    }else{
      return false;
    }
    
  });
  Handlebars.registerHelper("odd", function(options) {
    var intval = options.data.index / 2;
    if( intval === Math.ceil( intval ) ){
      return false;
    }else{
      return options.fn(this);
    }
  });
  Handlebars.registerHelper("json", function(context) {
      return JSON.stringify( context, null, 3 );
  });
  Handlebars.registerHelper(":node_point", function(context) {

      if( this._node_point ){
        var nodes = this._node_point.split('.'),
            node_point_record = nodes.join('][') + ']',
            node_path = node_point_record.replace( nodes[0] + ']', nodes[0] );

        return new Handlebars.SafeString( '<input type="hidden" name="' + node_path + '[_id]" value="' + nodes[nodes.length-1] + '"><input type="hidden" name="' + node_path + '[_node_point]" value="' + this._node_point + '">' );
      }

  });
  
  Handlebars.registerHelper(":name", function(context) {

      if( this._node_point ){
        var nodes = this._node_point.split('.'),
            node_point_record = nodes.join('][') + ']',
            node_path = node_point_record.replace( nodes[0] + ']', nodes[0] );

        return node_path;
      }

  });
  
  Handlebars.registerHelper('find', function(obj, field, options) {
    if( typeof obj === 'undefined' || typeof obj[field] === 'undefined' ){

        // check a nother level
        for( var part in obj ){

          if( typeof obj[part] !== 'undefined' && typeof obj[part][field] !== 'undefined' ){
            if( typeof obj[part][field] === 'string' && ( obj[part][field] === '' || obj[part][field] === null || obj[part][field] === false )  ){
              return options.inverse(this)
            }
            return options.fn(obj[part]);
          }
          if( obj[part] === field ){
            return options.fn(obj[part]);
          }
        }      
       return options.inverse(this)
    }else{

     return options.fn(obj[field]);
    }

  });
  Handlebars.registerHelper("is_single", function(value, options) {
    if(Object.keys(value).length !== 1){
      return false;
    }else{
      return options.fn(this);
    }
  });
  Handlebars.registerHelper("script", function(options) {
    var atts = [];
    for( var att in options.hash ){
      atts.push( att + '="' + options.hash[att] + '"' );      
    }

    return '<script ' + atts.join(' ') + '>' + options.fn(this) + '</script>';
  });
  Handlebars.registerHelper("is", function(value, options) {

    if( options.hash.value ){
      if(options.hash.value === '@key'){
        options.hash.value = options.data.key;
      }
      if(options.hash.value === value){
        return options.fn(this);
      }else{
        if(this[options.hash.value]){
          if(this[options.hash.value] === value){
            return options.fn(this);
          }
        }
        return options.inverse(this);
      }
    }
    if( options.hash.not ){
      if(options.hash.not === '@key'){
        options.hash.not = options.data.key;
      }
      if(options.hash.not !== value){
        return options.fn(this);
      }else{
        if(this[options.hash.not]){
          if(this[options.hash.not] !== value){
            return options.fn(this);
          }
        }
        return options.inverse(this);
      }
    }

  });

