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

			  <!-- Header -->
			  <?php include 'header.php'; ?>

				<!-- Main -->
					<div id="main" class="alt">

						<!-- One -->
							<section id="one">
								<div class="inner">
									<header class="major">
										<h1>Alignement</h1>
									</header>
		   
									<p>A 4th year Computer Science project, the goal was to use 2 child processes to play a game of Alignement. The rule of the game is fairly simple: 2 player play against each other on a 6 by 6 grid. Each turn, a player places a token anywhere on the grid that hasn’t been taken. The game continues until the grid is full, then the points are tallied. 2 aligned tokens are worth 5 points, 3 10 points, 4 are worth 20, 5 40, and 6 80 points. The winner is the player that has the most points.
									</p>
									<p>By using forks, the main process creates 2 child processes that will play the game. The communication between the 2 processes are done by changing the standard output of a command using tubes. This is to make sure one of the child process is waiting for the other to be done its turn.
									  
									  The read() and write() commands are very important for this project. When the read() function has been  called by a process, it just waits. This allows the player to take its time and think of its next move. That’s why the beginning of the player’s process starts with a read() function and ends with the write(). Once the grid is filled, one of the process tallies the points and writes the results that are then read by the father’s standard input. Finally, the father process calls the winner.									</p>
									
								</div>
							</section>

					<div class="inner">
					  <h2 id="code">Code</h2>
					  <pre><code>
  /*Simon Chiasson-St-Coeur
   *A00160309
   *esc7425
   */
#include &lt;iostream&gt;
#include &lt;cstdlib&gt;
#include &lt;unistd.h&gt;
#include &lt;sys/types.h&gt;
#include &lt;sys/wait.h&gt;
#include &lt;ctime&gt;

  using namespace std;

bool caseOccup;
int x, y;


//Affichage de la grille
void afficherGrille(char tab[][6])
{
  printf("%c|%c|%c|%c|%c|%c\n-----------\n",
	 tab[0][5],
	 tab[1][5],
	 tab[2][5],
	 tab[3][5],
	 tab[4][5],
	 tab[5][5]);
  printf("%c|%c|%c|%c|%c|%c\n-----------\n",
	 tab[0][4],
	 tab[1][4],
	 tab[2][4],
	 tab[3][4],
	 tab[4][4],
	 tab[5][4]);
  printf("%c|%c|%c|%c|%c|%c\n-----------\n",
	 tab[0][3],
	 tab[1][3],
	 tab[2][3],
	 tab[3][3],
	 tab[4][3],
	 tab[5][3]);
  printf("%c|%c|%c|%c|%c|%c\n-----------\n",
	 tab[0][2],
	 tab[1][2],
	 tab[2][2],
	 tab[3][2],
	 tab[4][2],
	 tab[5][2]);
  printf("%c|%c|%c|%c|%c|%c\n-----------\n",
	 tab[0][1],
	 tab[1][1],
	 tab[2][1],
	 tab[3][1],
	 tab[4][1],
	 tab[5][1]);
  printf("%c|%c|%c|%c|%c|%c\n",
	 tab[0][0],
	 tab[1][0],
	 tab[2][0],
	 tab[3][0],
	 tab[4][0],
	 tab[5][0]);
}


//Humain: O, Machine: X
void placerJeton(int x, int y, char pion, char tab[][6])
{
  tab[x][y] = pion;
}



int coup_Gagnant(char jeton, char tab[][6])
{
  int cur_count = 1;
  int max_length = 1;
  int point = 0;


  //Compte les point des lignes
  for(int i = 0; i &lt; 6; i++)
    {
      for (int j = 0; j &lt; 5; j++)
    	{
	  if(tab[j][i] == jeton && tab[j+1][i] == jeton)
	    cur_count++;

	  else
            {
	      switch(cur_count){
	      case 2: point = point + 5; break;
	      case 3: point = point + 10;break;
	      case 4: point = point + 20;break;
	      case 5: point = point + 40;break;
	      case 6: point = point + 80;break;
	      default: break;
	      }
	      cur_count = 1;
            }
    	}
    }


  //Compte les point sur les colonnes
  for(int i = 0; i &lt; 6; i++)
    {
      for (int j = 0; j &lt; 5; j++)
    	{
	  if(tab[i][j] == jeton && tab[i][j+1] == jeton)
	    cur_count++;

	  else
            {
	      switch(cur_count){
	      case 2: point = point + 5; break;
	      case 3: point = point + 10;break;
	      case 4: point = point + 20;break;
	      case 5: point = point + 40;break;
	      case 6: point = point + 80;break;
	      default: break;
	      }
	      cur_count = 1;
            }
    	}
    }

  return point;
};//coup_Gagnant


int main()
{
  int i, j, n, status, count, pointJoueur, pointMachine;
  pid_t fils1, fils2, pid;
  int p[2];
  pipe(p);
  bool cont = false;
  srand(time(nullptr));

  int n = 6;
  char grille[n][n];

  //Initialisation de la grille
  for(i = 0; i &lt; n; i++)
    for(j=0; j&lt;n; j++)
      grille[i][j] = ' ';
  count=0;

  if((fils1 = fork()) &lt; 0 ){
    printf("Erreur au premier fork\n");
    exit(1);
  }

  //processus de la machine
  else if (fils1 == 0)
    {
      while(count &lt; 36)
        {
	  do{
            x = rand()%6;
            y = rand()%6;

            if(grille[x][y] == ' ')
	      caseOccup = false;
            else
	      caseOccup = true;

	  }while(caseOccup); //Tant que la case est occupe, on en choisi une autre

	  write(p[1], &x, sizeof(x));
	  write(p[1], &y, sizeof(y));
	  write(p[1], &count, sizeof(count));
	  placerJeton(x, y, 'X', grille); //ecriture du jeton de la machine dans la grille de la machine

	  sleep(2);//sleep a des fin de synchronisation.
	  read(p[0], &i, sizeof(x));
	  read(p[0], &j, sizeof(y));
	  read(p[0], &count, sizeof(count));

	  placerJeton(i, j, 'O', grille);//ecriture du jeton du joueur dans la grille de la machine
	  count++;//inc. du count afin de savoir si la grille est pleine.
	}//while

      exit(0);
    }//elseif

  if ((fils2 = fork()) &lt; 0)
    {
      printf("Erreur au 2ieme fork\n");
      exit(1);
    }

  //processus du joueur
  else if(fils2 == 0)
    {
      while(count &lt; 35)
        {
	  read(p[0], &x, sizeof(x));
	  read(p[0], &y, sizeof(y));
	  read(p[0], &count, sizeof(count));

	  placerJeton(x, y, 'X', grille);//ecriture du jeton machine dans la grille du joueur
	  count++;//inc. du count afin de savoir si la grille est pleine
	  afficherGrille(grille);


	  do{
            printf("Entrez une coordonner:\n");

	    //COMMENT OUT THIS LINE IF YOU WOULD LIKE TO AUTOMATE THE TEST
            scanf("%d", &i); scanf("%d", &j);


   	    //UN-COMMENT THIS LINE IF YOU WOULD LIKE TO AUTOMATE THE TEST
	    //i = (rand()+5*8/4)%6; j = rand()%6;
            
	    
	    
	    if(grille[i][j] == ' ')
	      caseOccup = false;
            else if(i &gt; 5 || j &gt; 5)
	      {
                printf("La case doit etre entre 0 et 5\n");
                caseOccup = true;
	      }
            else
	      {
		printf("La case est deja choici, veuillez en prendre une autre\n");
		caseOccup = true;
	      }
	  }while(caseOccup); //Tant que la case est occupe, on demande d'en choisir une autre

	  write(p[1], &i, sizeof(i));
	  write(p[1], &j, sizeof(j));
	  write(p[1], &count, sizeof(count));
	  placerJeton(i, j, 'O', grille);//ecriture du jeton du joueur dans la grille du joueur

	  sleep(2);//Sleep a des fins de synchronisation.
        }//while

      //Calcul des pointages
      x = coup_Gagnant('O', grille);
      y = coup_Gagnant('X', grille);


      //Ecriture des pointages
      write(p[1], &x, sizeof(x));
      write(p[1], &y, sizeof(y));
      afficherGrille(grille);
      exit(0);
    }//elseif


  //Le pere attend qu’une partie se termine.
  pid = wait(&status);
  pid = wait(&status);

  //Match est termine, lecture des pointage
  read(p[0], &pointJoueur, sizeof(pointJoueur));
  read(p[0], &pointMachine, sizeof(pointMachine));

  //Affichage du gagnant
  if(pointJoueur &gt; pointMachine)
    printf("HUMAIN\n");
  else if(pointJoueur &lt; pointMachine)
    printf("ORDINATEUR\n");
  else
    printf("EGAL\n");

  exit(0);
} // main()

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
