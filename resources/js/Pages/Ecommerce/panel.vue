<template>
  <app-layout>
    <template #header>
      <ul class="flex">
        <li
          class="
            transition
            duration-200
            ease-linear
            flex
            hover:bg-blue-500
            hover:text-white
            items-center
            p-1.5
            rounded
          "
        >
          <inertia-link href="/admin/products"
            >Products
            <span
              class="
                bg-blue-900
                border-red-900
                h-24
                items-center
                justify-center
                px-3
                py-1
                rounded-full
                text-white
                w-24
              "
              >{{productCount}}</span
            ></inertia-link
          >
        </li>
        <li
          class="
            transition
            duration-200
            ease-linear
            flex
            hover:bg-blue-500
            hover:text-white
            items-center
            p-1.5
            rounded
          "
        >
          <inertia-link href="/admin/orders"
            >Orders
            <span
              class="
                bg-blue-900
                border-red-900
                h-24
                items-center
                justify-center
                px-3
                py-1
                rounded-full
                text-white
                w-24
              "
              >{{ordersCount}}</span
            ></inertia-link
          >
        </li>
        <li
          v-if="user.user_type=='admin'"
          class="
            transition
            duration-200
            ease-linear
            flex
            hover:bg-blue-500
            hover:text-white
            items-center
            p-1.5
            rounded
          "
        >
          <inertia-link href="/admin/ecommerce/category"
            >Categories
            <span
              class="
                bg-blue-900
                border-red-900
                h-24
                items-center
                justify-center
                px-3
                py-1
                rounded-full
                text-white
                w-24
              "
              >{{categoryCount}}</span
            ></inertia-link
          >
        </li>
        <li
          v-if="user.user_type=='admin'"
          class="
            transition
            duration-200
            ease-linear
            flex
            hover:bg-blue-500
            hover:text-white
            items-center
            p-1.5
            rounded
          "
        >
          <inertia-link href="/admin/ecommerce/vendors"
            >Vendors
            <span
              class="
                bg-blue-900
                border-red-900
                h-24
                items-center
                justify-center
                px-3
                py-1
                rounded-full
                text-white
                w-24
              "
              >{{vendorsCount}}</span
            ></inertia-link
          >
        </li>
      </ul>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
          <div class="grid grid-cols-2 gap-6">
          <div><canvas v-if="isOnEcomPage===true" id="myChart" width="50" height="25"></canvas></div>
          <div><canvas v-if="isOnEcomPage===true" id="myChart1" width="50" height="25" ></canvas></div>
        </div>
          <slot />
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Chart from 'chart.js/auto';
import { getRelativePosition } from 'chart.js/helpers'; 
export default {
  components: {
    AppLayout,
  },
  props: {
    isOnEcomPage: Boolean
  },
  data() {
    return {
      productCount: 0,
      categoryCount: 0,
      vendorsCount: 0,
      ordersCount: 0,
      user: {},
      month_sales: {},
      year: '',
      payments: {},
    };
  },
  methods: {
    async getSummary () {
      var _data = await fetch('/admin/ecommerce/summary').then(e=>e.json()).then(e=>{
        return e;
      });
      this.productCount = _data.productCount;
      this.categoryCount = _data.categoryCount;
      this.vendorsCount = _data.vendorsCount
      this.ordersCount = _data.ordersCount,
      this.user = _data.user
      this.month_sales = _data.month_sales
      this.year = _data.year;
      this.payments = _data.payments
    }
  },
  async mounted () {
    await this.getSummary();
    var payment_labels = [], payment_colors=[], payment_counts=[];
    var _default_colors = [
      'rgb(255, 99, 132)',
      'rgb(54, 162, 235)',
      'rgb(255, 205, 86)',
      'rgba(75, 192, 192, 1)',
    ];
    for(var p = 0; p < this.payments.length; p++){
      payment_labels.push(this.payments[p].payment_status);
      payment_colors.push(_default_colors[p])
      payment_counts.push(this.payments[p].total_orders);
    }
    const data = {
      labels: payment_labels,
      datasets: [{
        label: 'Payment Statuses in Orders Counts',
        data: payment_counts,
        backgroundColor: payment_colors,
        hoverOffset: payment_colors.length
      }]
    };
    const config = {
      type: 'pie',
      data: data,
    };
    var ctx1 = document.getElementById('myChart1');
    var myChart1 = new Chart(ctx1, config);
    var ctx = document.getElementById('myChart');
    var _labels = Object.keys(this.month_sales);
    var _sales = [];
    for(var q in this.month_sales){
      if(this.month_sales[q]){
        _sales.push(this.month_sales[q].total_orders)
      }else{
        _sales.push(0)
      }
    }
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: _labels,
            datasets: [{
                label: 'Yearly Sales '+this.year,
                data: _sales,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
  }
};
</script>
