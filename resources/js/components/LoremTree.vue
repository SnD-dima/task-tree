<template>
    <div class="search col-12">
        <div class="search-block col-12">
            <label for="search">Search</label>
            <input id="search" v-model="searchString" class="form-control form-group">
            <div class="list col-12" v-if="searchResults.length > 0">
                <div v-for="(item, index) in searchResults" class="list__item col-12" @click="selectSearchTree(item)">
                    <span class="list__item-index" v-html="(index + 1)"></span>

                    <Tree :value="item" class="list__item-tree">
                     <span slot-scope="{node, index, path, tree}" class="list__item-title">
                          {{ node.name }} {{ node.path }}
                        </span>
                    </Tree>

                </div>
            </div>
        </div>

        <div class="tree-result">
            <Tree :value="dataTree" droppable draggable  @drop="handleTreeChange">
            <span slot-scope="{node, index, path, tree}">
              <b @click="tree.toggleFold(node, path)">
                <span class="btn btn-sm"
                      v-if=" node.children.length > 0"
                      v-bind:class="{ 'btn-success': node.$folded, 'btn-primary': !node.$folded }"
                >
                    {{node.$folded ? '+' : '-'}}
                </span>
              </b>
              {{ node.name }} {{ node.path }}
                  <span class="btn btn-sm btn-danger float-right" @click="removeFromTree(node)">Delete</span>
            </span>
            </Tree>
        </div>

    </div>
</template>

<script>

    import {Tree, Fold, Check, Draggable,walkTreeData } from 'he-tree-vue'
    import 'he-tree-vue/dist/he-tree-vue.css'

    export default {
        data() {
            return {
                searchString: '',
                awaitingSearch: false,
                searchResults : [],
                dataTree: [...this.data]
            }
        },

        methods: {
            async handleTreeChange(store) {
                 let childElem = store.startTree.getNodeByPath(store.targetPath),
                    parentElem = store.startTree.getNodeParentByPath(store.targetPath);

                axios.post('/api/changePosition', {child : childElem, parent: parentElem})
                    .then(() => {
                    console.log('element is changed');
                    })
                    .catch(error => {
                        console.log('error',error);
                    });
            },
            selectSearchTree(id) {
                let elem = id[0].id;
                axios.get('/api/getById', {params : { 'id': elem } })
                    .then(response => {

                        this.dataTree = response.data;
                        this.searchTreeFold(elem);
                        this.searchResults = [];
                    })
                    .catch(error => {
                        this.searchResults = [];
                    });
            },
            searchTreeFold(id) {
                let fold = false;
                walkTreeData(this.dataTree, (node)  => {
                    if(node.id == id) {
                        fold = true;
                    }
                    if(fold) {
                        this.$set(node, "$folded", true);
                    }
                });
            },
            removeFromTree(node) {
                this.$set(node, "$hidden", true);
                axios.post('/api/deleteElement', {node : node})
                    .then(() => {
                    console.log('element is deleted');
                    })
                    .catch(error => {
                        console.log('error',error);
                });
            },
            searchTimeOut() {
                let timeout = null;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.search();
                }, 800);
            },
            search() {
                if(this.searchString.length > 1) {
                    axios.get('/api/search', {params : { 'name': this.searchString } })
                        .then(response => {
                            this.searchResults = response.data;
                        })
                        .catch(error => {
                            this.searchResults = [];
                    });
                }else{
                    this.searchResults = [];
                }
            }
        },
        mounted() {
            walkTreeData(this.dataTree, (node, index, parent, path)  => {
                if(path.length > 2) {
                  this.$set(node, "$folded", true);
                }
            });
        },
        watch: {
            searchString: function(){
                clearInterval(this.awaitingSearch);
                this.awaitingSearch = setTimeout(() => {
                    this.search();
                    }, 500);
                }
        },
        props: ['data'],
        components: {Tree: Tree.mixPlugins([Draggable,Fold,Check])},

    };
</script>

