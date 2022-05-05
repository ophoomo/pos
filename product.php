<?php
    session_start();
    require_once("controllers/DB.php");
    require_once("controllers/Product.php");
    require_once("controllers/Filesystem.php");
    require_once("controllers/Guard.php");
    Guard::member();
    $db = DB::conn();
    $result = [];
    $path = "storages/products";
    if(isset($_GET['id']) && isset($_GET['status'])) {
        $status = $_GET['status'];
        if($status === "remove") {
            $product = new Product($db);
            $product->id = $_GET['id'];
            $old_data = $product->findOne();
            $result = $product->delete();
            if ($result[0]) {
                $fs = new Filesystem();
                $fs->path = $path;
                $fs->delete($old_data[2]['image']);
            }

        } else if ($status === "update") {
            $product = new Product($db);
            $product->id = $_GET['id'];
            $product->name = $_POST['name'];
            $product->price = $_POST['price'];
            $product->stock = $_POST['stock'];
            $product->discount = $_POST['discount'];
            $result = $product->update();
        } else if ($status === 'change_image') {
            $product = new Product($db);
            $fs = new Filesystem();
            $fs->path = $path;
            $fs->setFile($_FILES['image']);
            $product->id = $_GET['id'];
            $old_data = $product->findOne();
            $product->image = $fs->getName();
            if ($old_data[0]) {
                $result = $product->changeImage();
            } else {
                $result = [false, "ไม่พบช้อมูล"];
            }
            if ($result[0]) {
                if($fs->upload()) {
                    $fs->delete($old_data[2]['image']);
                } else {
                    $db->rollBack();
                    $result = [false, "ไม่สามารถอัพโหลดไฟส์ได้"];
                }
            }
        }
    } else if(isset($_GET['status']) && $_GET['status'] === 'add') {
        $fs = new Filesystem();
        $fs->path = $path;
        $fs->setFile($_FILES['image']);
        $product = new Product($db);
        $product->name = $_POST['name'];
        $product->price = $_POST['price'];
        $product->stock = $_POST['stock'];
        $product->discount = $_POST['discount'];
        $product->image = $fs->getName();
        $result = $product->create();
        if ($result[0]) {
            if(!$fs->upload()) {
                $db->rollBack();
                $result = [false, "ไม่สามารถอัพโหลดไฟส์ได้"];
            }
        }
    } else if (isset($_GET['id'])) {
        $product = new Product($db);
        $product->id = $_GET['id'];
        $product_findOne_result = $product->findOne();
    }

    $product = new Product($db);
    $product_result = $product->find();
?>
<?php include_once('layouts/header.php'); ?>

<div class="col-lg-12 card">
    <div class="card-body">
        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#exampleModal">ADD</button>
        <table id="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Discount</th>
                    <th>Image</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($product_result[2] as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['price']; ?></td>
                    <td><?php echo $item['stock']; ?></td>
                    <td><?php echo $item['discount']; ?></td>
                    <td><button onclick="window.open('<?php echo $path.'/'.$item['image']; ?>', '','width=400,height=400')"
                        class="btn btn-primary">
                            <i class="fas fa-fw fa-image"></i></a>
                        </button></td>
                    <td>
                        <a href="product.php?id=<?php echo $item['id']; ?>&status=image" class="btn btn-info">
                            <i class="fas fa-fw fa-image"></i></a>
                        </a>
                        <a href="product.php?id=<?php echo $item['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-fw fa-edit"></i></a>
                        </a>
                        <a href="product.php?id=<?php echo $item['id']; ?>&status=remove" class="btn btn-danger">
                            <i class="fas fa-fw fa-trash"></i></a>
                        </a>
                    </td>
                </tr>
            <?php endforeach;  ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add" method="post" enctype="multipart/form-data" action="product.php?status=add">
            <div class="modal-body">
                <div class="row mt-2">
                    <div class="col">
                        <input required name="name" type="text" class="form-control" placeholder="Name">
                    </div>
                    <div class="col">
                        <input required name="price" type="number" min="0" class="form-control" placeholder="Price">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <input required name="stock" type="number" min="0" class="form-control" placeholder="Stock">
                    </div>
                    <div class="col">
                        <input required name="discount" type="text" class="form-control" placeholder="Discount">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <input required name="image" type="file" class="form-control" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">ADD</button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php if(isset($product_findOne_result)): ?>
<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="edit_modal_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_modal_label">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="add" method="post" action="product.php?id=<?php echo $product_findOne_result[2]['id']; ?>&status=update">
                <div class="modal-body">
                    <div class="row mt-2">
                        <div class="col">
                            <input required value="<?php echo $product_findOne_result[2]['name']; ?>"
                                   name="name" type="text" class="form-control" placeholder="Name">
                        </div>
                        <div class="col">
                            <input required value="<?php echo $product_findOne_result[2]['price']; ?>"
                                   name="price" type="number" min="0" class="form-control" placeholder="Price">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input required value="<?php echo $product_findOne_result[2]['stock']; ?>"
                                   name="stock" type="number" min="0" class="form-control" placeholder="Stock">
                        </div>
                        <div class="col">
                            <input value="<?php echo $product_findOne_result[2]['discount']; ?>"
                                   required name="discount" type="text" class="form-control" placeholder="Discount">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if(isset($_GET['id']) && isset($_GET['status']) && $_GET['status'] === 'image'): ?>
    <div class="modal fade" id="image_modal" tabindex="-1" role="dialog" aria-labelledby="image_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="image_modal_label">Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add" method="post" enctype="multipart/form-data" action="product.php?id=<?php echo $_GET['id']; ?>&status=change_image">
                    <div class="modal-body">
                        <div class="row mt-2">
                            <div class="col">
                                <input required name="image" type="file" class="form-control" >
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include_once('layouts/bottom.php'); ?>
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script>
    $(document).ready( function () {
        $('#table').DataTable();
        <?php if(isset($product_findOne_result)): ?>
            $('#edit_modal').modal('show');
        <?php endif; ?>
        <?php if(isset($_GET['id']) && isset($_GET['status']) && $_GET['status'] === 'image'): ?>
        $('#image_modal').modal('show');
        <?php endif; ?>
    } );
</script>
