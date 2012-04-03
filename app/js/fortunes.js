var updateVote = function(request){
    result = eval('('+request.responseText+')');
    if (result.id){
        $('vote'+result.id).innerHTML = 'vote <span>'+result.result+'</span>';
    }else{
        alert('Can not update vote');
    }
}

var activateVotes = function(){
    var forms = document.getElementsByTagName('form');
    for(i=0; i<forms.length; i++){
        if (forms[i].className == 'vote-form'){
            btBury = document.getElementsByClassName('bury',forms[i])[0];
            btVote = document.getElementsByClassName('vote',forms[i])[0];
            btBury.onclick = function(){
                id = this.parentNode.childNodes[1].value;
                url = 'api.php?ajax=1';
                params = 'bury=1&id='+id;
                var ajax = new Ajax.Request(url, {method: 'post', parameters:params, onComplete:updateVote});
                return false;
            }
            btVote.onclick = function(){
                id = this.parentNode.childNodes[1].value;
                url = 'api.php?ajax=1';
                params = 'vote=1&id='+id;
                var ajax = new Ajax.Request(url, {method: 'post', parameters:params, onComplete:updateVote});
                return false;
            }
        }
    }
}

Event.observe(window, 'load', activateVotes, false);