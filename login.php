<?php if(!isset($Translation)) { @header('Location: index.php?signIn=1'); exit; } ?>
<?php include_once("$currDir/header.php"); ?>

<?php if($_GET['loginFailed']) { ?>
	<div class="alert alert-danger"><?php echo $Translation['login failed']; ?></div>
<?php } ?>

<div class="row">
	<div class="col-sm-6 col-lg-8" id="login_splash">
		<h1><strong>Sistema de Control de Pagos</strong></h1>
			<div id="accordion" class="myaccordion">
			  <div class="card">
			    <div class="card-header" id="headingOne">
			      <h2 class="mb-0">
			        <button class="d-flex align-items-center justify-content-between btn btn-link collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne"><?php echo $Translation['members2']; ?>:<span class="fa-stack fa-sm">
			            <i class="fas fa-circle fa-stack-2x"></i>
			            <i class="fas fa-plus fa-stack-1x fa-inverse"></i>
			          </span>
			        </button>
			      </h2>
			    </div>
			    <div id="collapseOne" aria-labelledby="headingOne" data-parent="#accordion">
			      <div class="card-body">
			        <ul>
			          <li><?php echo $Translation['a5']; ?>   -   <?php echo $Translation['ma5']; ?></li>
			          <li><?php echo $Translation['a4']; ?>   -   <?php echo $Translation['ma4']; ?></li>
			          <li><?php echo $Translation['a3']; ?>   -   <?php echo $Translation['ma3']; ?></li>
			          <li><?php echo $Translation['a2']; ?>   -   <?php echo $Translation['ma2']; ?></li>
			          <li><?php echo $Translation['a1']; ?>   -   <?php echo $Translation['ma1']; ?></li>
			        </ul>
			      </div>
			    </div>
			  </div><br>
			  <div>
			  	<p>
			  		<a href="documentacion/index.html">Consulta la documentación del aplicativo.</a>
			  	</p>
			  </div>
			</div>

	</div>
	<div class="col-sm-6 col-lg-4">
		<div class="panel panel-success">

			<div class="panel-heading">
				<h1 class="panel-title"><strong><?php echo $Translation['sign in here']; ?></strong></h1>
				<?php if(sqlValue("select count(1) from membership_groups where allowSignup=1")) { ?>
					<a class="btn btn-success pull-right" href="membership_signup.php"><?php echo $Translation['sign up']; ?></a>
				<?php } ?>
				<div class="clearfix"></div>
			</div>
			
			<div class="panel-body">
				<form method="post" action="index.php">
					<div class="form-group">
						<label class="control-label" for="username"><?php echo $Translation['username']; ?></label>
						<input class="form-control" name="username" id="username" type="text" placeholder="<?php echo $Translation['username']; ?>" required>
					</div>
					<div class="form-group">
						<label class="control-label" for="password"><?php echo $Translation['password']; ?></label>
						<input class="form-control" name="password" id="password" type="password" placeholder="<?php echo $Translation['password']; ?>" required>
						<span class="help-block"><?php echo $Translation['forgot password']; ?></span>
					</div>
					<div class="checkbox">
						<label class="control-label" for="rememberMe">
							<input type="checkbox" name="rememberMe" id="rememberMe" value="1">
							<?php echo $Translation['remember me']; ?>
						</label>
					</div>

					<div class="row">
						<div class="col-sm-offset-3 col-sm-6">
							<button name="signIn" type="submit" id="submit" value="signIn" class="btn btn-primary btn-lg btn-block"><?php echo $Translation['sign in']; ?></button>
						</div>
					</div>
				</form>
			</div>

			<?php if(is_array(getTableList()) && count(getTableList())) { /* if anon. users can see any tables ... */ ?>
				<div class="panel-footer">
					<?php echo $Translation['browse as guest']; ?>
				</div>
			<?php } ?>

		</div>
	</div>
</div>

<script>document.getElementById('username').focus();</script>
<?php include_once("$currDir/footer.php"); ?>
