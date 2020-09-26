<!DOCTYPE HTML>
<!--
	Forty by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title>Real Time Computing</title>
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
									  <h1>Real Time Computing</h1>
									</header>
									<p>
									  Computing the positions of an object in real time presents many challenges, since there are many sources of delays that needs to be accounted for: receiving data, making calculation, etc. For this project we needed to use 3 different methods of calculating an integral. One would provide an over-estimation, an other an under-estimation, and the 3rd one somewhere in the middle. On top of those, I've created a 4th method that would intentionally be wrong.</p>
									
<p>Since the 3rd method provides the most accurate result, all the other methods corrects themselves with its value, if and only if all 3 accurate methods are within a 0.005m margin. If we are over that margin we use the solution computed by the over-estimation method.</p>
<p>The reason for the correction is to save time and to avoid the gap between the over-estimation and under-estimation to widen over time. We save time because we shorten the integral that needs to be calculated by using the Sum Rule: the limit of a sum equals the sum of the limits.</p>
<p><b>NB:</b> To make the project more challenging and fun for myself I created a Server – Client relationship. Please contact me if you would like a copy of the full code.</p>
									</p>
								</div>
							</section>

					<div class="inner">
					  <h2 id="code">Code</h2>
					  <pre><code>
*Chiasson-St-Coeur Simon
 * info 4009 Devoir 2
 * serveur.cpp
 */

#include &lt;stdio.h&gt;
#include &lt;string.h&gt;
#include &lt;signal.h&gt;
#include &lt;sys/types.h&gt;
#include &lt;netinet/in.h&gt;
#include &lt;sys/socket.h&gt;
#include &lt;arpa/inet.h&gt;
#include &lt;fcntl.h&gt;
#include &lt;netdb.h&gt;
#include &lt;unistd.h&gt;
#include &lt;stdlib.h&gt;
#include &lt;iostream&gt;
#include "integrale.h"

#define PORT_NUM 1670
#define MAX_LISTEN 5

using namespace std;


//Variables Globales
char			out_buf[100];
char 			in_buf[100];
unsigned int 		server_s = 0;
pthread_t 		threads[10];
struct sockaddr_in 	server_addr;
unsigned int 		client_s;
struct sockaddr_in 	client_addr;
struct in_addr 		client_ip_addr;
unsigned int 		addr_len;
int 			*ptr[10];

//Signature des fonctions
void *fonc(void *);



class Prog
//La classe abstraite que les 4 programmes vont suivre.
{
	private:
	float table_Val[50];
	short curr;

	public:
	Prog() {curr = 0;};
	~Prog(){};

	void set_Resultat(float valeur)
	//L'integrale d'une somme est la somme des integrales, nous utilisons les anciens
	//resultats pour calculer le resultat courant.
	{
		if(curr != 0)

			table_Val[curr++] = table_Val[curr-1] + valeur;
		else
			table_Val[curr++] = valeur;
	}

	void corr_Resultat(float valeur)
		//Si une correction doit avoir lieu, cette fonction est apelle
	{
		if(curr != 0)
			table_Val[curr-1] = valeur;
		else
			table_Val[curr] = valeur;
	}

	int get_curr()
	{
		return curr;
	}

	void inc_curseur()
	{
		curr++;
	}

	float get_Val()
		//get_Val() par default, puisque le curseur est incremente lorsqu'on ajoute un resultat, get_Val()
		//donne valeur de la case precedente.
	{
		return table_Val[curr-1];
	}

	float get_Val(int pos)
	{
		return table_Val[pos];	
	}
};//Prog



class Prog1: public Prog
//Le 1er programme fait une sous-estimation en utilisant les sommes de Reimann
{
	public:
	Prog1() {};
	~Prog1(){};

	float sous_estimation(int debut, int fin, Graphe &graphe)
	{
		float resultat = 0;
		for(int i = debut; i &lt; fin; i++)
			resultat = resultat + (graphe.get_Point(i, 1) * 0.1);
		set_Resultat(resultat);
		return get_Val();
	}
};//Prog1

class Prog2 : public Prog
//Le 2ieme programme fait une sur-estimation en utilisant les sommes de Reimann
{
	public:
	Prog2() {};
	~Prog2(){};

	float sur_estimation(int debut,int fin, Graphe &graphe)
	{
		float resultat = 0;
		for(int i = debut + 1; i &lt;= fin; i++)
			resultat = resultat + (graphe.get_Point(i, 1) * 0.1);
		set_Resultat(resultat);
		return get_Val();
	}
};//Prog2

class Prog3 : public Prog
//Le 3ieme programme fait une estimation en utilisant la methode de trapeze
{
	public:
	Prog3() {};
	~Prog3(){};

	float trapeze(int min, int max, Graphe &graphe)
	{
		float resultat = graphe.get_Point(min, 1) + graphe.get_Point(max, 1);
		
		float delta = (((max-min)*0.1)/(max-min))/2;

		for(int i = min + 1; i &lt; max; i++)
			resultat = resultat + 2*(graphe.get_Point(i, 1));

		set_Resultat(delta*resultat);

		return get_Val();
	}
};//Prog3

class ProgErr : public Prog
//Programme avec une erreur, on ajoute 0,03m au resultat.
{
	public:
	ProgErr(){};
	~ProgErr(){};

	float trapezeErr(int min, int max, Graphe &graphe)
	{
		float resultat = graphe.get_Point(min, 1) + graphe.get_Point(max, 1);
		
		float delta = (((max-min)*0.1)/(max-min))/2;

		for(int i = min + 1; i &lt; max; i++)
			resultat = resultat + 2*(graphe.get_Point(i, 1));

		set_Resultat(delta*resultat+0.03);

		return get_Val();
	}

	void affiche_vals()
	{	cout &lt;&lt; "Valeurs enregistre de ProgErr:\n";
		for(int i = 0; i &lt; get_curr(); i++)
			cout &lt;&lt; get_Val(i) &lt;&lt; endl;
	}

};

int main()
{
pthread_setconcurrency(4);


server_s = socket(AF_INET, SOCK_STREAM, 0);
server_addr.sin_family = AF_INET;
server_addr.sin_port = htons(PORT_NUM);
server_addr.sin_addr.s_addr = htonl(INADDR_ANY);

bind(server_s, (struct sockaddr *)&server_addr, sizeof(server_addr));

listen(server_s, MAX_LISTEN);

while (1) {

	addr_len = sizeof(client_addr);
	client_s = accept(server_s, (struct sockaddr *)&client_addr, &addr_len);
	pthread_create(&threads[client_s-4], NULL, fonc ,&client_s);

}

pthread_join(threads[client_s-4],(void **)&(ptr[client_s-4]));


close(server_s);

return 0;

}//main()




void *fonc(void *arg)
{
	int 	socketClient = * (int *) arg;
	float	valeur;
	int	i = 0;

	Graphe integrale;
	Prog1 prog1;
	Prog2 prog2;
	Prog3 prog3;
	ProgErr prog3_err;

	srand(time(NULL));

	//On envoie au serveur abcde pour confirmer que la connection est bien etablie
	//et que nous sommes prets a recevoir les donnees
	sleep(1);
	strcpy(out_buf, "abcde");
	cout &lt;&lt; out_buf &lt;&lt;endl;
	send(socketClient, out_buf, (strlen(out_buf)+1), 0);
	printf("Sent\n");


	recv(socketClient, in_buf, sizeof(in_buf), 0);
	cout &lt;&lt; in_buf &lt;&lt; endl;
	while(in_buf[0] != 'f')
	{
		valeur = float(atof(in_buf));
		integrale.place_Point(i++, 1, valeur);
               //Envoi au client qu'on a recu et enregistre la valeur, alors on est pret pour recevoir la prochaine
               strcpy(out_buf, "go");
		send(socketClient, out_buf, (strlen(out_buf)+1), 0);
		recv(socketClient, in_buf, sizeof(in_buf), 0);
	}//while

	integrale.afficher_Graphe();

	for(int j = 10; j &lt; 150; j += 10)
	{
		prog1.sous_estimation(j-10, j, integrale);
		prog2.sur_estimation(j-10, j, integrale);
		prog3.trapeze(j-10, j, integrale);
		prog3_err.trapezeErr(j-10, j, integrale);


	cout &lt;&lt; "Iteration " &lt;&lt; j/10 &lt;&lt; endl &lt;&lt; "Valeur de la sous-estimation: " &lt;&lt; prog1.get_Val() &lt;&lt;
	"\nValeur de la sur-estimation: " &lt;&lt; prog2.get_Val() &lt;&lt;
	"\nValeur de la methode trapeze: " &lt;&lt; prog3.get_Val() &lt;&lt; endl;
	prog3_err.affiche_vals();
	cout &lt;&lt; endl;

	//Corrections des valeurs s'il y a lieu, la marge est de 0.5cm.
	//Si toutes les resultats sont dans cette marge, alors prog1 et prog2 corriges leurs
	//valeurs avec celle obtenue par prog3, c-a-d, selon la methode trapeze
	if(abs(prog1.get_Val() - prog3.get_Val()) &gt; 0.005){
		prog1.corr_Resultat(prog2.get_Val());
		prog3.corr_Resultat(prog2.get_Val());
		}
	else{
		prog1.corr_Resultat(prog3.get_Val());
		prog2.corr_Resultat(prog3.get_Val());
		}
	
	if(abs(prog1.get_Val() - prog3_err.get_Val()) &gt; 0.005){
		prog1.corr_Resultat(prog2.get_Val());
		prog3_err.corr_Resultat(prog2.get_Val());
		}
	
	//Sleep pour simuler un deplacement par seconde.
	sleep(1);	
	}//for

	return NULL;
}//fonc
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
