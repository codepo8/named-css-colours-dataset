(function(){
    var levels = document.querySelector('#levels');
    var query = document.querySelector('#query');
    var result = document.querySelector('#result');
    var gameover = document.querySelector('#gameover');
    var resultslist = document.querySelector('#list');
    var gamedata = {};
    function trap(ev) {
        ev.preventDefault();
    }
    function game(ev){
        if (ev.target.tagName !== 'BUTTON') { return; }
        document.querySelector('#intro').style.display = 'none';
        gameover.innerHTML = '';
        var leveldata = ev.target.getAttribute('data-level').split('-');
        fetch('api.php?showlist='+leveldata[1]);
        gamedata.amount = leveldata[1];
        gamedata.moves = leveldata[2];
        gamedata.level = leveldata[0];    
        gamedata.corrects = 0;
    }
    function showresults(col) {
        query.innerHTML = '<p>Find the colour ' + gamedata.name +' - ' + gamedata.moves + ' tries left</p>';
        if (col) {
            result.innerHTML = '<p>Nope… ' + gamedata.colours[col] +'</p>';
        } else {
            result.innerHTML = '';
        }
    }
    function check(ev) {
        if (ev.target.tagName !== 'BUTTON') { return; }
        var col = (ev.target.value);
        if (col === gamedata.value) {
            gamedata.corrects++;
            fetch('api.php?showlist='+gamedata.amount);
        } else {
            if (gamedata.moves > 1) {
                gamedata.moves--;
                showresults(col);
            } else {
                result.innerHTML = '';
                query.innerHTML = '';
                resultslist.innerHTML = '';
                gameover.innerHTML = '<h2>Game over!</h2>' +
                                    '<p>You recognised '+gamedata.corrects+' colours on the ' +
                                    gamedata.level + ' level. Try again?</p>';
                levels.style.display = 'block';
            }
        }
    }
    function listready(list){
        levels.style.display = 'none';
        gamedata.name = list.match(/data-target="([^"]*)"/)[1].split('x')[1];
        gamedata.value = list.match(/data-target="([^"]*)"/)[1].split('x')[0];
        gamedata.colours = JSON.parse(list.match(/\{[^\}]*\}/)[0]);
        list = list.replace(/{.*}/,'');
        showresults();
        resultslist.innerHTML = list;
    }
    function fetch(url){
        var request = new XMLHttpRequest();
        request.open('get',url,true);
        request.onreadystatechange=function(){
        if(request.readyState == 4){
            if (request.status && /200|304/.test(request.status))
            {
            listready(request.responseText);
            } 
        }
        }
        request.setRequestHeader('If-Modified-Since','Wed, 05 Apr 2006 00:00:00 GMT');
        request.send(null);
    }
    list.addEventListener('click',check);
    document.querySelector('form').addEventListener('submit',trap);
    document.querySelector('#levelbuttons').addEventListener('click',game);
})();