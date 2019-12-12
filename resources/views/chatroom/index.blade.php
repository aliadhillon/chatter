@extends('layouts.app')

@section('content')
        <div class="card w-25 float-right mr-5">
            <div class="card-body">
                <div v-for="poke in pokes" class="alert alert-danger">
                    @{{ poke.message }}
                    <button type="button" class="close" @click="clearPoke(poke)">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div v-if="alert" class="alert alert-success">
                    @{{ alert }}
                    <button type="button" class="close" @click="clearAlert()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <h5 class="card-title">
                    Users Online
                </h5>
                <ul>
                    <li v-for="user in users" class="card-text mb-3">
                        @{{ user.name }} 
                        <button @click="poke(user)" class="btn btn-sm btn-warning float-right mr-5">Poke</button>
                    </li>
                </ul>
            </div>
        </div>
    <div class="container">
        <div class="container">
            <div v-for="message in messages" class="card w-75 mb-1">
                <div class="card-body">
                    <h5 v-if="message.user.name == user.name" class="card-title text-warning d-inline mb-3">You</h5>
                    <h5 v-else class="card-title text-success d-inline mb-3">@{{ message.user.name }}</h5>
                    <span class="text-primary float-right">@{{ message.created_at }}</span>
                    <p class="card-text">@{{ message.body }}</p>
                </div>
            </div>
        </div>
        <div class="container mt-5 mb-5" id="send">
            <div class="form-group  w-75">
                <input class="form-control mb-1" type="text" name="message" id="message" size="10" v-model="messageBox">
                <button type="submit" class="btn btn-lg btn-success float-right" @click.prevent="postMessage()">Send</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                messages: [],
                messageBox: '',
                user: {!! Auth::check() ? Auth::user()->toJson() : 'null' !!},
                users: [],
                pokes: [],
                alert: null,
            },
            mounted(){
                this.getMessages();
                this.listen();
            },
            methods: {
                getMessages(){
                    const AUTH = 'Bearer '.concat(this.user.api_token);
                    axios.get('/api/messages', {
                        headers: {
                            Authorization: AUTH 
                        }
                        })
                        .then((response) => {
                            this.messages = response.data.data;
                        })
                        .catch(function(error){
                            console.log(error);
                        })
                },
                postMessage(){
                    axios.post('/api/messages', {
                        api_token: this.user.api_token,
                        body: this.messageBox
                    })
                    .then((response) => {
                        this.messages.push(response.data.data);
                        this.messageBox = '';
                    })
                    .catch(function(error){
                        console.log(error);
                    })
                },
                poke(user){
                    const AUTH = 'Bearer '.concat(this.user.api_token);
                    axios.get('/api/poke/' + user.id, {
                        headers: {
                            Authorization: AUTH
                        }
                    })
                    .then((response) => {
                        this.alert = response.data.message;
                    });
                },
                clearPoke(pokeRemove){
                    this.pokes = this.pokes.filter((poke) => {
                        return poke.id !== pokeRemove.id;
                    }); 
                },
                clearAlert(){
                    this.alert = null;
                },
                listen(){
                    Echo.join('messages')
                        .listen('NewMessage', (message) => {
                            this.messages.push(message);
                        })
                        .here((users) => {
                            this.users = users.filter((user) => {
                                return user.id !== this.user.id;
                            });
                        })
                        .joining((user) => {
                            this.users.push(user);
                        })
                        .leaving((userLeft) => {
                            this.users = this.users.filter((user) => {
                                return user.id !== userLeft.id;
                            })
                        })
                        .whisper('typing', {
                            name: this.user.name
                        })
                        .listenForWhisper('typing', (e) => {
                            console.log(e.name);
                        });

                        Echo.private('App.User.' + this.user.id)
                        .notification((notification) => {
                            this.pokes.push(notification);
                        });
                }
            }
        });
    </script>
@endsection
