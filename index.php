<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.16/vue.js"></script>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div id="app">
        <div class="textOutput">
            <table>
                <tr v-for="msg in msgArr">
                    <th>{{ msg.user_id }}</th>
                    <td>{{ msg.chat_msg }}</td>
                </tr>
            </table>
        </div>
        <div class="textInput">
            <input type="text" v-model="typeMsg">
            <button @click="sendMsg">送出</button>
        </div>
    </div>
    <script>
        new Vue({
            el : '#app',
            data: {
                userId : Math.floor(Math.random() * 100 ),
                msgArr : [],
                typeMsg: '',
                wsServer : new WebSocket('ws://localhost:8080/')
            },
            mounted: function() {
                this.setWebSocket();
            },
            methods : {
                setWebSocket : function() {
                    this.wsServer.onopen = (e) => {
                        let getMessage = JSON.stringify({
                            type : 'socket',
                            user_id : this.userId
                        });
                        this.wsServer.send(getMessage);
                    }
                    this.wsServer.onerror = e => {}
                    this.wsServer.onmessage = e => {
                        let json = JSON.parse(e.data);
                        if(json.type == 'chat') this.msgArr.push(json);
                    }
                },
                sendMsg : function () {
                    let sendMessage = JSON.stringify({
                        type : 'chat',
                        user_id : this.userId,
                        chat_msg : this.typeMsg
                    });
                    this.wsServer.send(sendMessage);
                    this.typeMsg = '';
                }
            }
        })
    </script>
</body>
</html>