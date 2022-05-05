<?php
    session_start();
    require_once("controllers/DB.php");
    $db = DB::conn();
    require_once("controllers/Auth.php");
    if(isset($_POST['username'])) {
        $auth = new Auth($db);
        $auth->username = $_POST['username'];
        $auth->password = $_POST['password'];
        $result = $auth->Login();

        if($result[0]) {
            $_SESSION['id'] = $result[2]['id'];
            $_SESSION['status'] = $result[2]['status'];
            $_SESSION['name'] = $result[2]['firstname']." ".$result[2]['lastname'];
            header("Location: index.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>POS System</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <script src="https://unpkg.com/vue@3"></script>
    <div id="app" class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center align-items-center vh-100">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form id="submit" action="" method="post" class="user">
                                        <div class="form-group">
                                            <input @keydown.enter="onSubmit()" v-model="username" type="email" class="form-control form-control-user"
                                                   :class="{'is-invalid': error.username}"
                                                name="username" aria-describedby="emailHelp"
                                                placeholder="Enter Email Address...">
                                        </div>
                                        <div class="form-group">
                                            <input @keydown.enter="onSubmit()" v-model="password" type="password"
                                                   :class="{'is-invalid': error.password}"
                                                   class="form-control form-control-user"
                                                name="password" placeholder="Password">
                                        </div>
                                        <button @click="onSubmit()" type="button" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        Vue.createApp({
            created() {
                <?php if(isset($result)): ?>
               if (<?php echo !$result[0]; ?>) {
                   Swal.fire({
                       icon: 'error',
                       title: 'เกิดข้อผิดพลาด',
                       text: '<?php echo $result[1];  ?>',
                   });
               }
               <?php endif; ?>
            },
            data() {
                return {
                    username: "",
                    password: "",
                    error: {
                        username: false,
                        password: false,
                    }
                }
            },
            methods: {
                validateEmail(email) {
                    return String(email)
                        .toLowerCase()
                        .match(
                        /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    );
                },
                isEmail() {
                    this.error.username = true;
                    if (this.username == "") {
                        document.getElementsByName("username")[0].focus();
                        return false;
                    }
                    if (!this.validateEmail(this.username)) {
                        document.getElementsByName("username")[0].focus();
                        return false;
                    }
                    this.error.username = false;
                    return true;
                },
                isPassword() {
                    this.error.password = true;
                    if(this.password == "") {
                        document.getElementsByName("password")[0].focus();
                        return false;
                    }
                    if (this.password.length < 10) {
                        document.getElementsByName("password")[0].focus();
                        return false;
                    }
                    this.error.password = false;
                    return true;
                },
                onSubmit() {
                    if (
                        this.isEmail() && this.isPassword()
                    ) {
                        document.getElementById("submit").submit();
                    }
                }
            }
        }).mount('#app');
    </script>

</body>

</html>