<template>
    <div>
      <h1>Product Index</h1>
      <ul>
        <li v-for="product in products" :key="product.id">
          {{ product.name }} - ${{ product.price }}
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ProductList',
    data() {
      return {
        products: [
          { id: 1, name: 'Product A', price: 50 },
          { id: 2, name: 'Product B', price: 30 },
          { id: 3, name: 'Product C', price: 20 },
        ]
      };
    }
  };
  </script>
  
  <style scoped>
  h1 {
    font-size: 2em;
  }
  ul {
    list-style-type: none;
    padding: 0;
  }
  li {
    margin: 0.5em 0;
  }
  </style>
  