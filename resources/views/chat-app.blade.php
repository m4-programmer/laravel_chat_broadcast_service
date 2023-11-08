<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel Websockets Chat Example</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js" integrity="sha512-XdUZ5nrNkVySQBnnM5vzDqHai823Spoq1W3pJoQwomQja+o4Nw0Ew1ppxo5bhF2vMug6sfibhKWcNJsG8Vj9tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
{{--    @vite(['resources/sass/app.scss', 'resources/js/app.js'])--}}
</head>
<body>
    <div class="container" id="app">
        <h1 class="text-center mt-4">Laravel Websockets Chat Example</h1>
        <div class="card mt-4">
            <div class="card-header pt-2">
                <form action="">
                    <div class="col-lg-2 col-md-3 col-sm-12 mt-2 p-0">
                        <label for="">Name</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Name" v-model="name">
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-12 mt-2 p-0 "
                         style="display: flex;justify-content: space-between">
                        <button v-if="connected === false"
                                v-on:click="connect()"
                                class="mr-2 mx-2 btn btn-sm btn-primary w-100"
                                type="button"
                        >
                            Connect
                        </button>
                        <button v-if="connected === true"
                                v-on:click="disconnect()"
                                type="button"
                                class="mr-2 btn btn-sm btn-danger w-100"
                        >
                            Disconnect
                        </button>
                    </div>
                </form>
                <div>
                    <p>Channel current state is @{{ state }}</p>
                </div>
            </div>
            <div v-if="connected === true" class="card-body">
                <div class="col-12 bg-light pb-2 mt-3">
                    <p class="p-0 m-0 ps-2 pe-2" v-for="(message,index) in incomingMessages">
                        (@{{message.time}}) <b>@{{ message.name }}</b>
                        @{{ message.message }}
                    </p>
                </div>
                <h4 class="mt-4">Message</h4>
                <form action="">
                    <div class="row mt-2">
                        <div class="col-12 text-white" v-show="formError === true">
                            <div class="bg-danger p-2 mb-2">
                                <p class="p-0 m-0">
                                    <b>Error:</b> Invalid Message.
                                </p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <textarea v-model="message" id="" cols="3" rows="3" class="form-control" placeholder="Your Message...">

                                </textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row text-right mt-2">
                        <div class="col-lg-10">

                        </div>
                        <div class="col-lg-2">
                            <button type="button" v-on:click="sendMessage()" class="btn btn-success btn-small w-100">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        new Vue({
            'el':"#app",
            'data':{
                connected: false,

                pusher: null,
                app: null,
                apps: {!!  json_encode($apps) !!},
                logChannel: "{{ $logChannel }}",
                authEndpoint: "{{ $authEndpoint }}",
                host: "{{ $host }}",
                port: "{{ $port }}",

                state: null,
                name: null,
                formError: false,
                incomingMessages: [
                    // {
                    //     message: "new message",
                    //     name: "Miracle",
                    //     time: "2023"
                    // },
                ],
                message: null,

            },
            mounted() {
                this.app = this.apps[0] || null;
            },
            methods: {
                connect(){
                    // Enable Pusher debug mode, this will help u debug ur connection
                    Pusher.logToConsole = true;

                    this.pusher = new Pusher("asdfg",{
                        cluster: "mt1",
                        wsHost: this.host,
                        wsPort: this.port,
                        wssPort: this.port,
                        wsPath: this.app.path,
                        disabledStats: false,
                        authEndpoint: this.authEndpoint,
                        forceTLS: false,//change this to true for wss (secured version)
                        auth: {
                            headers: {
                                "X-CSRF-Token": "{{ csrf_token() }}",
                                "X-App-ID": this.app.id,
                            },
                        },
                        enabledTransports: ['ws', 'flash']
                    });
                    console.log(this.state)
                    console.log(this.pusher)
                    this.pusher.connection.bind('state_change', states => {
                        this.state = states.current;
                        console.log('Connection state:', states.current);
                        console.log('Status code:', states.status);
                        console.log('Status :', states);

                    });
                    this.pusher.connection.bind('connected', () => {
                        console.log('Connected to Pusher');
                        this.connected = true;
                    });
                    this.pusher.connection.bind('disconnected', () => {
                        console.log('Disconnected from Pusher');
                        this.connected = false;
                    });
                    this.pusher.connection.bind('error', event => {
                        this.formError = true;
                    });


                },
                disconnect(){
                    this.connected = false;
                },
                sendMessage(){
                    this.formError = true;
                }
            }
        });
    </script>
</body>
</html>
