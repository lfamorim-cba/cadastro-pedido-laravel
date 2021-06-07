<template>
    <pagina>
        <div class="col-md-12">
            <acoes titulobotao="Adicionar cliente" modal="modal-adicionar" v-bind:termodebusca="busca" v-on:input="busca = $event"></acoes>

            <div v-if="loader.clientes" class="alert alert-warning">Carregando lista...</div>

            <div v-if="erro.status" class="alert alert-danger">Ocorreu um erro ao obter dados...</div>

            <mensagem v-if="msg.status" v-bind:tipo="msg.tipo">{{msg.texto}}</mensagem>

            <tabela v-if="clientes != '' && !loader.clientes && !erro.status" v-bind:colunas="['#', 'Nome','Data', 'Ações']" v-bind:filtro="busca">
                <tr v-for="(cliente, index) in filtro" v-bind:key="index">
                    <td>{{ cliente.id }}</td>
                    <td>{{ cliente.nome }}</td>
                    <td>{{ cliente.data }}</td>
                    <td>
                        <button class="btn btn-warning" v-on:click="editar(cliente)">Editar</button>
                        <button class="btn btn-danger" v-on:click="deletar(cliente.id)">Excluir</button>
                    </td>
                </tr>
            </tabela>

            <pagination :data="clientes" @pagination-change-page="listar"></pagination>

            <div class="alert alert-warning" v-if="clientes == '' && !loader.clientes">Nenhum cliente foi cadastrada</div>

            <modal nome="modal-adicionar" v-on:acao="adicionar(cliente)" titulo="Adicionar cliente" textobotao="adicionar">                                
                <div class="form-group row">
                    <div class="col-md-12" v-bind:class="{ invalid: statusForm == false && $v.cliente.nome.$invalid }">
                        <label for="nome">Nome do cliente</label>
                        <input type="text" name="nome" id="nome" placeholder="Nome do cliente" class="form-control" v-model="$v.cliente.nome.$model">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12" v-bind:class="{ invalid: statusForm == false && $v.checkbox.$invalid }">
                        <label for="nome">Maior de 18 Anos</label>
						<input type="checkbox" name="checkbox-1"  id="checkbox-1" checked="checked">
                    </div>
                </div>
                <div v-if="statusForm == false && $v.cliente.nome.$invalid" class="alert alert-danger">Insira um nome para registrar a cliente</div>
           </modal>

            <modal nome="modal-editar" v-on:acao="adicionar(cliente)" titulo="Editar cliente" textobotao="Salvar alterações">                               
                <div class="form-group row">
                    <div class="col-md-12" v-bind:class="{ invalid: statusForm == false &&  $v.cliente.nome.$invalid }">
                        <label for="nome">Nome da cliente</label>
                        <input type="text" name="nome" id="nome" placeholder="Nome da cliente" class="form-control" v-model="$v.cliente.nome.$model">
                    </div>
                </div>
                <div v-if="statusForm == false && $v.cliente.nome.$invalid" class="alert alert-danger">Insira um nome para registrar a cliente</div>
            </modal>
        </div>
    </pagina>
</template>

<script>

import cliente from "@/js/services/cliente";
import { required } from "vuelidate/lib/validators";

export default {
    data() {
        return {
            clientes: {},
            cliente: {
                nome: '',
            },
            busca: '',
            loader: {
                clientes: false
            },
            erro: {
                status: false
            },
            msg: {
                status: false,
                tipo: '',
                texto: '',
            },
            statusForm: null
        }
    },
    methods: {
        mensagem: function (status, tipo, texto) {
            this.msg.status = status;
            this.msg.tipo = tipo;
            this.msg.texto = texto;
            setTimeout(() => {
                this.msg.status = false;
            }, 5000);
        },
        adicionar: function (data) {
            var vm = this;
            if (!this.$v.$invalid) {
                this.statusForm = true;
                if (vm.cliente.id) {
                    cliente.atualiza(data.id, data).then(function (response) {
                        vm.cliente = {};
                        vm.$v.$reset();
                        vm.listar();
                        $('#modal-editar').modal('hide');
                        vm.mensagem(true, 'sucesso', 'Alterações salvas com sucesso');
                    }).catch(function () {
                        vm.cliente = {};
                        vm.$v.$reset();
                        $('#modal-editar').modal('hide');
                        vm.mensagem(true, 'erro', 'Ocorreu um erro ao salvar as alterações feitas na cliente');
                    })
                } else {
                    cliente.cadastra(data).then(function (response) {
                        vm.cliente = {};
                        vm.$v.$reset();
                        vm.listar();
                        $('#modal-adicionar').modal('hide');
                        vm.mensagem(true, 'sucesso', 'cliente registrado com sucesso');
                    }).catch(function () {
                        vm.cliente = {};
                        vm.$v.$reset();
                        $('#modal-adicionar').modal('hide');
                        vm.mensagem(true, 'erro', 'Ocorreu um erro ao registrar o cliente');
                    })
                }
            } else {
                this.statusForm = false;
            }
        },
        listar: function (page = 1) {
            var vm = this;
            this.loader.clientes = true;
            cliente.lista(page).then(function (response) {
                vm.clientes = response.data;
            }).catch(function () {
                vm.erro.status = true;
            })
            .finally(function () {
                vm.loader.clientes = false;
            })
        }, 
        editar: function (data) {
            var vm = this;
            vm.cliente = data;
            $('#modal-editar').modal('show');
        }, 
        deletar: function (id) {
            var vm = this;
            if (confirm("Tem certeza que deseja excluir este item?")) {
                cliente.apaga(id).then(function (response) {
                    vm.listar();
                    vm.mensagem(true, 'sucesso', 'cliente excluída com sucesso');
                }).catch(function () {
                    vm.mensagem(true, 'erro', 'Ocorreu um erro ao excluír esta cliente');
                })
            }
        }  
    },
    computed: {
        filtro: function () {
            if (!this.busca) return this.clientes.data;
            return this.clientes.data.filter((dado) => {
                return dado.nome.toLowerCase().includes(this.busca.toLowerCase());
            })
        }
    },
    validations: {
        cliente: { 
            nome: { required }
        }
    },
    mounted() {
        this.listar();
    }
}
</script>

<style>
    .invalid {
        border-color: red;
    }

    .invalid input, .invalid select {
        border-color: red;
    }

    label {
        font-weight: bold;
    }
</style>