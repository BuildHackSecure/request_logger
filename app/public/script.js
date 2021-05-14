function RequestCatcher(session,data) {

    let last_id = 0;

    let methods = {
        loadData    :   function(){
            let xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    let json = JSON.parse(this.responseText);
                    if( json.hasOwnProperty('data') ){
                        data = json.data;
                        methods.logData();
                    }
                }
            };
            xhttp.open("GET", '/' + session + '.json', true);
            xhttp.send();
        },
        stampToDate :   function(i){
            let date = new Date(i * 1000);
            let month = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            return date.getDate()+' '+month[date.getMonth()] +' '+date.getFullYear()+' '+date.getHours().toString().padStart(2, '0')+':'+date.getMinutes().toString().padStart(2, '0')+':'+date.getSeconds().toString().padStart(2, '0') +' UTC';
        },
        logData :   function(){
            for( let x in data ){
                if( parseInt(x) > last_id ) {
                    last_id = parseInt(x);
                    let tmp_div = document.createElement('DIV');
                    tmp_div.dataset.id = x;
                    tmp_div.innerHTML = data[x].type.toUpperCase() + '<br>' + methods.stampToDate(data[x].created_at);
                    tmp_div.addEventListener('click', function () {
                        let html = '';
                        let r = data[this.dataset.id];
                        if( r.type === 'dns' ){
                            html = 'We received a DNS lookup with type: ' + r.request_type + ' for the domain:<br><strong>' + r.domain + '</strong><br><br>The Lookup was requested @ <strong>' + methods.stampToDate(r.created_at) + '</strong> from IP <strong>' + r.ip + '</strong>';
                        }else{
                            html = 'We received the following HTTP Request:<br>----------------------------------------------------------------------------------<pre>';
                            for( let x2 in r.request ){
                                html += r.request[x2]+'<br>';
                            }
                            html += '</pre>----------------------------------------------------------------------------------<br>Request received @ <strong>' + methods.stampToDate(r.created_at) + '</strong> from IP <strong>' + r.remote_ip + '</strong>'
                        }
                        document.getElementsByClassName('content')[0].innerHTML=html;
                    });
                    document.getElementsByClassName('requests')[0].prepend(tmp_div);
                }
            }
        }
    }
    setInterval(methods.loadData,2500);
    methods.logData();
}
