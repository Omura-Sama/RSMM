<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<style>
body {
    font-family: "Lato", sans-serif;
}



.main-head{
    height: 150px;
    background: #FFF;
   
}

.sidenav {
    height: 100%;
    background-color: #000;
    overflow-x: hidden;
    padding-top: 20px;
}


.main {
    padding: 0px 10px;
}

@media screen and (max-height: 450px) {
    .sidenav {padding-top: 15px;}
}

@media screen and (max-width: 450px) {
    .login-form{
        margin-top: 10%;
    }

    .register-form{
        margin-top: 10%;
    }
}

@media screen and (min-width: 768px){
    .main{
        margin-left: 40%; 
    }

    .sidenav{
        width: 40%;
        position: fixed;
        z-index: 1;
        top: 0;
        left: 0;
    }

    .login-form{
        margin-top: 80%;
    }

    .register-form{
        margin-top: 20%;
    }
}


.login-main-text{
    margin-top: 20%;
    padding: 60px;
    color: #fff;
}

.login-main-text h2{
    font-weight: 300;
}

.btn-black{
    background-color: #000 !important;
    color: #fff;
}
</style>

<script>
var myWindow;
</script>

<div class="sidenav">
         <div class="login-main-text">
            <h2>Reset Password<br> Anda</h2>
            <!-- <p>Login or register from here to access.</p> -->
         </div>
      </div>
      <div class="main">
         <div class="col-md-6 col-sm-12">
            <div class="login-form">
               <form action="http://izeber.xyz/index.php/Reset/resetPassword" method="POST">
               <!-- <form action="http://7252056ba6d8.sn.mynetname.net/index.php/Reset/resetPassword" method="POST"> -->
                  <div class="form-group">
                     <label>New Password</label>
                     <input hidden type="input" name="email" class="form-control" value="<?= $_GET['email']; ?>" >
                     <input hidden type="input" name="nik" class="form-control" value="<?= $_GET['nik']; ?>">
                     <input type="password" name="newpass" class="form-control" placeholder="New Password" require>
                  </div>
                  <div class="form-group">
                     <label>Confirm Password</label>
                     <input type="password" name="confirmpass" class="form-control" placeholder="Confirm Password" require>
                  </div>
                  <button type="submit" class="btn btn-black">Submit</button>
                  <!-- <button type="submit" class="btn btn-secondary">Register</button> -->
               </form>
            </div>
         </div>
      </div>