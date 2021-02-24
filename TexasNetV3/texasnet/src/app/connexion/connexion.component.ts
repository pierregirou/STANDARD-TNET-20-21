import { Component, OnInit } from '@angular/core';
import { NgForm } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { AuthService } from '../services/auth.service';
import { HttpClient } from '@angular/common/http';
import { ProduitService } from '../services/produits.service';
import { ModuleService } from '../services/modules.service';
import { MatSnackBar } from '@angular/material';
import { CommandeService } from '../services/commandes.service';
import { TranslateService,LangChangeEvent } from '@ngx-translate/core';

@Component({
  selector: 'app-connexion',
  templateUrl: './connexion.component.html',
  styleUrls: ['./connexion.component.css']
})
export class ConnexionComponent implements OnInit {
  iconStatut:string="lock";
  authStatut:boolean;
  visGalerie:boolean;
  hide:boolean=true; //variable permettant d'afficher ou non le mot de passe en claire : true par défaut (caché)
  msgMaintenance:string="";
  msgConnexion:string="";
  disableConnexion = false;
  constructor(private commandeService:CommandeService, private router:Router, private authService : AuthService, private httpClient : HttpClient, private route : ActivatedRoute,private produitService:ProduitService, private moduleService : ModuleService,private snackBar:MatSnackBar,translate: TranslateService) {
    if(sessionStorage.getItem("isLoggedIn")==="true"){
      this.router.navigate(['contenu/accueil']);
    }
    translate.get('message.connexion').subscribe((res: string) => {
      this.msgConnexion = res;
    });
    translate.get('message.maintenance').subscribe((res: string) => {
      this.msgMaintenance = res;
    });

    translate.onLangChange.subscribe((event: LangChangeEvent) => {
      translate.get('message.connexion').subscribe((res: string) => {
        this.msgConnexion = res;
      });
      translate.get('message.maintenance').subscribe((res: string) => {
        this.msgMaintenance = res;
      });
    });
   }

  ngOnInit() {
    var textConnexion = "Merci d'utiliser Chrome ou Firefox pour saisir vos commandes. Internet Explorer et Safari ne sont pas supportés par ce site. "
    const isIEOrEdge = /msie\s|trident\/|edge\//i.test(window.navigator.userAgent)
    if(isIEOrEdge) {
      this.disableConnexion = true;
      alert('Impossible de commander avec le navigateur Microsoft Edge ou Internet Explorer !')
    }

    var ua = navigator.userAgent.toLowerCase();
    if (ua.indexOf('safari') != -1) {
      if (ua.indexOf('chrome') > -1) {
      } else {
        this.disableConnexion = true;
        alert('Impossible de commander avec le navigateur Safari !')
      }
    }
    this.authStatut=this.authService.isAuth; //met la valeur de isAUth de authService dans authService
  }
  onSubmit(form:NgForm){
    const login = form.value["login"];
    const password = form.value["password"];
    this.iconStatut="spinner"; //Lorsque l'utilisateur appuie sur connexion lance un spinner avec la propriété spin true
    this.authService.signIn(login,password).then( //appel de la fonction signIn du service authService qui renvoie une nouvelle promise
      (value)=>{
        if(value){
          this.authStatut=this.authService.isAuth;
          sessionStorage.setItem('isLoggedIn','true'); //session storage permettant de vérifier la bonne connexion à la plateforme
          sessionStorage.setItem('loginCompte',login); //session storage contenant le login de l'utilisateur connecté
          this.authService.loginCompte=sessionStorage.getItem('loginCompte');
          this.router.navigate(['contenu/accueil']); //si la promise renvoie true (isAuth===true) redirige vers contenu
          this.commandeService.initCommande(); //initialise la commande lors de la connexion sur la plateforme
        }else{
          this.moduleService.infoModules().then(data=>{
            this.iconStatut="times-circle";  //si la promise renvoie false (isAuth===false) indique que la connexion a échoué

            if(data["maintenance"]==1){
              this.snackBar.open(this.msgMaintenance,"",{duration:3000}); //affiche un snackBar à l'utilisateur si le login ou le mot de passe est incorrect
            }else{
              this.iconStatut="times-circle";  //si la promise renvoie false (isAuth===false) indique que la connexion a échoué
              this.snackBar.open(this.msgConnexion,"",{duration:3000}); //affiche un snackBar à l'utilisateur si le login ou le mot de passe est incorrect
            }
            setTimeout(
              ()=>{
                this.iconStatut="lock"; //au bout de 3 sec le cadenas revient et le snackbar s'enlève également
              },3000
            );
          })

        }
      }
    )
  }
}
