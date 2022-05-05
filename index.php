<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Guard.php");
    require_once("controllers/Product.php");
    require_once("controllers/Customer.php");
    Guard::member();
    $db = DB::conn();
    $product = new Product($db);
    $product_result = $product->find();

    $customer = new Customer($db);
    $customer_result = $customer->find();
?>
<?php include_once('layouts/header.php'); ?>

<script src="https://unpkg.com/vue@3"></script>
<div id="app" class="row">
    <div class="col-lg-8 mt-2">
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-center flex-wrap" style="gap: 10px">

                <div :key="index" v-for="(item, index) in data.products" @click="add_order(item)"
                     @contextmenu="contextMenu($event, item)" class="product">
                    <div v-if="item.stock == 0" class="overlay"></div>
                    <div v-html="select_product(item)"></div>
                    <img :src="'storages/products/' + item.image" style="width:100%" alt="NoImage"/>
                    <div class="product-header">
                        <h6 v-if="Number.parseInt(item.discount) > 0" class="text-danger"
                            style="text-decoration: line-through">${{ item.price }}</h6>
                        <h5 class="text-success">฿{{cal_discount(item)}}</h5>
                        <h4 :title="item.name" >{{ item.name }}</h4>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-4 mt-2">
        <div class="card">
            <div class="card-body">
                <h3 class="text-center text-success">ราคาทั้งหมด {{ total }} บาท</h3>
                <table class="table">
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr :key="index" v-for="(item, index) in this.input.orders" class="text-center">
                            <td>{{ index+1 }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ cal_discount(item) }}</td>
                            <td width="80px"><input :max="item.stock" v-model="item.qty"
                                                    @change="check_stock(item)" type="number" min="1" class="form-control" /></td>
                            <td>{{ cal_discount(item) * item.qty }}</td>
                            <td><button @click="remove_order(item)" class="btn btn-danger btn-sm">X</button></td>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <label for="customer_tel">เบอร์โทรของลูกค้า</label>
                    <input id="customer_tel" v-model="input.customer" @change="check_tel()"
                           :class="{ 'is-invalid': check_customer  }" class="form-control" />
                </div>
                <button :disabled="input.orders.length === 0 || check_customer" @click="checkout()"
                        class="btn btn-success w-100 mt-2">Checkout</button>
                <button :disabled="input.orders.length === 0" @click="clear()"
                        class="btn btn-outline-danger mt-2 w-100">Clear</button>
            </div>
        </div>
    </div>
</div>



<?php include_once('layouts/bottom.php'); ?>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    Vue.createApp({
        data() {
            return {
                data: {
                    products: <?php echo json_encode($product_result[2]); ?>,
                    customers: <?php echo json_encode($customer_result[2]); ?>,
                },
                input: {
                    orders: [],
                    customer: "",
                },
                check_customer: false,
                total: 0,
            }
        },
        methods: {
            cal_total() {
                let sum = 0;
                this.input.orders.forEach(item => {
                    sum += this.cal_discount(item) * item.qty;
                });
                this.total = sum;
            },
            check_stock(product) {
                if (product.qty < 0) {
                    product.qty = 1;
                } else if (product.qty > product.stock) {
                    product.qty = product.stock;
                }
                this.cal_total();
            },
            check_tel() {
                if (this.input.customer != '') {
                    const find = this.data.customers.find(item => item.tel == this.input.customer);
                    if (find !== undefined) {
                        this.check_customer = false;
                    } else {
                        this.check_customer = true;
                    }
                } else {
                    this.check_customer = false;
                }
            },
            contextMenu(e, product) {
                if (product.stock > 0) {
                    this.down_qty(product)
                }
                e.preventDefault();
            },
            add_order(product) {
                if (product.stock > 0) {
                    const find = this.input.orders.find(item => item.id == product.id);
                    if (find !== undefined) {
                        const check = this.input.orders.indexOf(find);
                        if (this.input.orders[check].qty < product.stock) {
                            this.input.orders[check].qty += 1;
                        }
                    } else {
                        this.input.orders.push({...product, qty: 1});
                    }
                    this.cal_total();
                }
            },
            down_qty(product) {
                const find = this.input.orders.find(item => item.id == product.id);
                if (find !== undefined) {
                    const check = this.input.orders.indexOf(find);
                    if (this.input.orders[check].qty > 1) {
                        this.input.orders[check].qty -= 1;
                    } else {
                        this.remove_order(product);
                    }
                }
                this.cal_total();
            },
            remove_order(product) {
                const find = this.input.orders.find(item => item.id == product.id);
                if (find !== undefined) {
                    const index = this.input.orders.indexOf(find);
                    this.input.orders.splice(index, 1);
                }
                this.cal_total();
            },
            checkout() {
                this.cal_total();
                Swal.fire({
                    icon: 'question',
                    title: 'ราคาทั้งหมด ' + this.total,
                    title: 'เงินที่รับมา',
                    input: 'number',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                }).then(result => {
                    if (result.value !== undefined) {
                        if (result.value != '' && result.value >= this.total) {
                            let id_customer = null;
                            if (this.input.customer != '') {
                                const find_customer = this.data.customers.find(item => item.tel === this.input.customer);
                                id_customer = find_customer.id;
                            }
                            axios.post("add_order.php", {
                                customer: id_customer,
                                products: JSON.stringify(this.input.orders),
                                total: this.total,
                                received: result.value,
                            }, {
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                }
                            });
                            Swal.fire({
                                icon: 'success' ,
                                title: 'ชำระเงินเสร็จสิ้น',
                                text: `รับเงินมา ${result.value} บาท ทอน ${result.value - this.total} บาท`,
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            this.checkout();
                        }
                    }
                });
            },
            clear() {
                this.input.orders = [];
                this.cal_total();
            },
            cal_discount(product) {
                const check_discount = product.discount.search("%");
                if (check_discount > -1) {
                    const split_discount = product.discount.split("%");
                    const discount = Number.parseFloat(split_discount[0]);
                    return product.price - (product.price * discount / 100);
                } else {
                    const discount = Number.parseFloat(product.discount);
                    return product.price - discount;
                }
            },
            select_product(product) {
                for(let item of this.input.orders) {
                    if (item.id === product.id) {
                        return `<div class="bg-danger text-white select">${item.qty}</div>`;
                    }
                }
            }
        }
    }).mount('#app');

</script>
