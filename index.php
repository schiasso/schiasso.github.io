<!DOCTYPE HTML>
<html>
    <head>
        <title>My Portfolio</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
        <link rel="stylesheet" href="assets/css/main.css" />
        <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    </head>
    <body class="is-preload">

        <!-- Wrapper -->
        <div id="wrapper">
	    <!-- Header -->>
	    <?php 
	    include 'header.php';
	    ?>
	    <!-- Banner -->
	    <section id="banner" class="major">
		<div class="inner">
		    <header class="major">
			<h1>Hi, my name is Simon</h1>
		    </header>
		    <div class="content">
			<p>Graduated from Université de Moncton<br />
			    This is a collection of my projects</p>
			<ul class="actions">
			    <li><a href="#one" class="button next scrolly">Check it out</a></li>
			</ul>
		    </div>
		</div>
	    </section>
	    
	    <!-- Main -->
	    <div id="main">
		
						<!-- One -->
		<section id="one" class="tiles">
		    <article>
			<span class="image">
			    <img src="images/pic01.jpg" alt="">
			</span>
			<header class="major">
			    <h3><a href="matrix.php" class="link">Matrix Multiplication</a></h3>
			    <p>Using parallel computing in C </br>
				the goal of this project was to compute
				the product of 2 matrices
				using the MPI library</p>
			</header>
		    </article>
		    
		    <article>
			<span class="image">
			    
			    <img src="images/pic02.jpg" alt="" />
			</span>
			<header class="major">
			    <h3><a href="3pluscontent.php" class="link">Marketing and Communication</a></h3>
			    <p>
				I created the content for a total of 32 web pages for the Economic
				Development Agency
				of my city in the summer of 2020. The main objective was to rehaul the
				currentwebsite and to promote to our community the services offered by the agency.
			    </p>
			</header>
		    </article>
		    <article>
			<span class="image">
			    <img src="images/pic03.jpg" alt="" />
			</span>
			<header class="major">
			    <h3><a href="alignements.php" class="link">6*6 Array "Alignements" Game</a></h3>
			    <p>Using forks in C++, the objectif was to create two child processus,
				and they would play a game of "Alignements". The communication between the
				processes would be done via tubes.</p>
			</header>
		    </article>
		    <article>
			<span class="image">
			    <img src="images/pic04.jpg" alt="" />
			</span>
			<header class="major">
			    <h3><a href="realtime.php" class="link">Real Time Computing</a></h3>
			    <p>Using C++ a server receives current speeds of a client every miliseconds. The server then computes the distances
				covert by the client using 4 different integrals calculation.</p>
			</header>
		    </article>
		</section>
		
		
		<section id="two">
		    <div class="inner">
			<header class="major">
			    <h2>About me</h2>
			</header>
			<p>I've always been driven to learn new things. During my university years I've taken a lot of classes not directly related to Computer Science to expand my skill set to the world of business. Management and Political Science classes taught me a lot about project management and the public sector.</p>
			<ul class="actions">
			    <li><a href="https://docs.google.com/document/d/1mCWgOEfYqUU91H_N1R58bXssgDU1SuxcSSVV9DrJbIw/edit?usp=sharing" class="button next">My Resume</a></li>
			</ul>
		    </div>
		</section>
		
	    </div>
	    
	    
	    <!-- Footer -->
	    <?php 
	    include 'footer.php';
	    ?>
	    
	    <!-- Scripts -->
	    <script src="assets/js/jquery.min.js"></script>
	    <script src="assets/js/jquery.scrolly.min.js"></script>
	    <script src="assets/js/jquery.scrollex.min.js"></script>
	    <script src="assets/js/browser.min.js"></script>
	    <script src="assets/js/breakpoints.min.js"></script>
	    <script src="assets/js/util.js"></script>
	    <script src="assets/js/main.js"></script>
	    
    </body>
</html>
