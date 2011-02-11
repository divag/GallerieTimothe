function encode(texte)
{
	return escape( encodeURIComponent( texte ) );
}
function decode(s)
{
  return unescape( decodeURIComponent(s));
}

function htmlEncode(value){ 
  return $('<div/>').text(value).html(); 
} 

function htmlDecode(value){ 
  return $('<div/>').html(value).text(); 
}

function getDatas(functionName, variableName, functionParams)
{
    var urlFunction = urlBaseFunction + functionName + '.php';

    if (functionParams != '')
        functionParams = 'variable=' + variableName + '&' + unescape(functionParams);
    else
        functionParams = 'variable=' + variableName;
    
    $.ajax({  url: urlFunction, 
              type: 'POST', 
              processData: false,
              data: functionParams, 
              async: false,
    error: function(){
        			alert('Problème de connexion au serveur... Il vaut mieux partir...');
		},
		success: function(data){
        			eval(data);
		}	
	});
}

