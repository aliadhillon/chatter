@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>
                
                <div v-if="poke" class="alert alert-success">
                    @{{ poke.message }}
                    <button type="button" class="close" @click.prevent="clearPoke()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        const app = new Vue({
            el: '#app',
            data: {
                poke: null,
                user: {!! Auth::check() ? Auth::user()->toJson() : 'null' !!}
            },
            mounted(){
                this.listen();
            },
            methods: {
                listen(){
                    Echo.private('App.User.' + this.user.id)
                        .notification((notification) => {
                            this.poke = notification;
                        })
                },
                clearPoke(){
                    this.poke = null;
                }
            }
        });
    </script>
@endsection
