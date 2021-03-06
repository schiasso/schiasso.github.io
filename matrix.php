<!DOCTYPE HTML>
<!--
	Forty by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Matrix Multiplication</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">

		<!-- Wrapper -->
		<div id="wrapper">
		  <!-- Header-->
		  <?php include 'header.php'; ?>
		  
		  <!-- Main -->
		  <div id="main" class="alt">
		    
		    <!-- One -->
		    <section id="one">
		      <div class="inner">
			<header class="major">
			  <h1>Matrix Multiplication</h1>
			</header>
			<p>In order to save time executing complex tasks, it is often useful to utilize parallel computing concepts.  For this project the goal was to use the functions of the MPI library in order to establish a communication between processors.
			  
			  The code starts by creating the 2 matrices and then broadcasts the whole matrix B to all processors. Then each processor receives nbln lines of matrix A, by using the <code>MPI_Scatter</code> function. Every processors now have <code>nbln</code> lines of matrix A written in their respective buffer, they then use that buffer and the matrix B to write the product of AxB in matrix C. The resulting matrix is then gathered by processor 0 and displayed. This project was done using C, and should be compiled with <code>mpicc</code>.</p>
		      </div>
		    </section>
		    
		    <div class="inner">
		      <h2 id="code">Code</h2>
		      <pre><code>
  #include "mpi.h"
  #include &lt;stdio.h&gt;

  int main(int argc,char *argv[])
  {
    int p, myid, source, dest, nbln, deb_ligne,i,j,k,rc;
    const int nla=12;    /* Nombre de lignes dans la matrice A */
    const int nca=11;    /* Nombre de colonnes dans la matrice A */
    const int ncb=9;   /* Nombre de colonnes dans la matrice B */
    int  root=0, tag1=1, tag2=2;
    double	 a[nla][nca],           /* matrice A  */
      b[nca][ncb],           /* matrice B */
      c[nla][ncb],           /* matrice résultat  C */
      buff[nla][nca];	/* buff des comm. collectives*/
    
    MPI_Status status;
    MPI_Init(&argc,&argv);
    MPI_Comm_size(MPI_COMM_WORLD,&p);
    MPI_Comm_rank(MPI_COMM_WORLD,&myid);
  
    /* on suppose nla divise p*/
    nbln  = nla/p;
    if (myid == root)
      {
	for (i=0; i&lt;nla; i++)
	  for (j=0; j&lt;nca; j++)
	    a[i][j]= i+j;
	
	for (i=0; i&lt;nca; i++)
	  for (j=0; j&lt;ncb; j++)
	    b[i][j]= i*j;
      }
    
    MPI_Bcast(&b ,nca*ncb, MPI_DOUBLE, 0, MPI_COMM_WORLD); /*Broadcast de la matrice B*/ 
    
    /*Scatter de nbln lignes au processeurs aux processeurs*/
    MPI_Scatter(&a, nca*nbln, MPI_DOUBLE, &buff, nca*nbln, MPI_DOUBLE, 0, MPI_COMM_WORLD);
    
    /*calcul du pr duit de la matrice AxB */
    for (k=0; k&lt;ncb; k++)
      for (i=0; i &lt; nbln; i++)
	{
	  c[i][k] = 0.0;
	  for (j=0; j&lt;nca; j++)
	    c[i][k] = c[i][k] + buff[i][j] * b[j][k];
	}
    
    MPI_Gather(&c, nbln*ncb, MPI_DOUBLE, &c, nbln*ncb, MPI_DOUBLE, 0, MPI_COMM_WORLD);
    
    /* affichage des  résultats */
    if(myid == 0){
      printf("Matrice résultat \n");
      for (i=0; i&lt;nla; i++)
	{
	  printf("\n");
	  for (j=0; j&lt;ncb; j++)
	    printf("%6.2f   ", c[i][j]);
	}
      printf ("\n");
    }
    MPI_Finalize();
  }
  </code></pre>
  </div>
  </div>
  
		  
		  <!-- Footer -->
<?php include 'footer.php'; ?>

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
