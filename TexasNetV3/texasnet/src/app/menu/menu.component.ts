import { Component, OnInit, Input, OnDestroy } from '@angular/core';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';
import { MenuService } from '../services/menu.service';
import { Subscription, Observable } from 'rxjs';
import 'rxjs/add/observable/interval';
import { HttpClient } from '@angular/common/http';
import { InformationService } from '../services/informations.service';
import { ModuleService } from '../services/modules.service';
import { HttpRequest } from '../services/http-request.service';
import { CommandeService } from '../services/commandes.service';
import { ImageService } from '../services/images.service';
import { SousMenu1 } from "../models/SousMenu1.models";
import { LangueService } from '../services/langue.service';
import { TemplateService } from '../services/template.service';
import { TranslateService } from '@ngx-translate/core';
import { BreakpointObserver } from '@angular/cdk/layout';
import { ProduitService } from '../services/produits.service';

@Component({
  selector: 'app-menu',
  templateUrl: './menu.component.html',
  styleUrls: ['./menu.component.css']
})
export class MenuComponent implements OnInit, OnDestroy {
  @Input() authStatut:boolean;
  @Input() compte:string;
  panelOpenState = false;
  choix:number ;
  maLangue:string;
  langueSelect:number; //permet de connaître la langue choisie
  langueSelectSubscription:Subscription; //subscription de langue select depuis le service langue
  visGalerie:boolean;
  maintenance:boolean;
  langueAng:boolean;
  paramPoints:boolean;
  paramPromo:boolean;
  paramLook:boolean;
  points:number;
  pointSubscription:Subscription;
  menuNavbar:any;
  modeleArray:SousMenu1[]=[];
  modeleArrayContenu:any[]=[];
  menuColor:string;
  menuColorSubscription:Subscription;
  nbActifSubMenu:number; //permet de savoir le nombre de sous-menus activés
  approuveur:boolean;
  societe:string='';
  recherche:string;
  MenuArrayDoubleSubscription:Subscription;
  cle1:string;
  mobNav:boolean = false;

  constructor(private templateService:TemplateService, private breakpointObserver:BreakpointObserver,private langueService:LangueService, public imageService:ImageService,private commandeService:CommandeService,private httpRequest:HttpRequest, private authService:AuthService, private router:Router,private httpClient:HttpClient,private informationService:InformationService, private moduleService:ModuleService,private translate: TranslateService, private produitService: ProduitService, private menuService:MenuService) {
    //initialisation de points au début du programme
    this.httpClient.post(this.httpRequest.Informations,{'login':sessionStorage.getItem("loginCompte")}).subscribe(data=>{
      if(data){
        this.points=data["points"];
      }
    })

    this.mobNav = this.breakpointObserver.isMatched('(min-width: 992px)');
    this.breakpointObserver.observe([
      '(max-width: 991px)'
    ]).subscribe(result => {
      this.mobNav = result.matches;
    });
  }
  ngOnInit() {
    this.MenuArrayDoubleSubscription=this.menuService.menuSubject.subscribe(
      (menu:any[])=>{
       // console.log(menu)
        this.menuNavbar = menu;
      });
    this.menuService.initialiseMenu();
    this.menuService.detecteLangueMenu();

    this.httpClient.post(this.httpRequest.InfoParametrages,{
      "parametrages":"ok"
    }).subscribe(data=>{
      this.societe=data[1].nomSociete;
    });

    this.httpClient.post(this.httpRequest.MenuInfo,{
      "choix":"activeSubMenu"
    }).subscribe(data=>{
      this.nbActifSubMenu=data["nbActif"];
    });

    this.templateService.getMenuColor();
    this.menuColorSubscription=this.templateService.menuColorSubject.subscribe(
      (menuColor:string)=>{
        this.menuColor='#'+menuColor;
      }
    );
    this.templateService.emitMenuColor();

    /* Choix langue */
    this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(
      (selectLangue:number)=>{
        this.langueSelect=selectLangue;
        if (selectLangue === 1){
          this.langueAng = false;
        } else {
          this.langueAng = true;
        }
      }
    );
    this.langueService.emitLangueSelect();

    //récupère le nombre de points de informations.service.ts déclaré en privé==>impossible de modifier sa valeur depuis les autres parties de l'appli
    this.pointSubscription=this.informationService.pointSubject.subscribe(
      (points:number)=>{
        this.points=points;
      }
    )
    Observable.interval(10000000).subscribe(x=>{
      this.onRecupPoints();
    })

    // Activer Visuel Galerie
    this.moduleService.visGalerieStatus().then(data=>{
      if (data === 0){
        this.visGalerie = false;
      } else {
        this.visGalerie = true;
      }
    })

    // Activer/Desactiver Point
    this.moduleService.ActivatePoints().then(data=>{
      if (Number(data['points']) === 0){
         this.paramPoints = false;
       } else {
         this.paramPoints = true;
       }
     })

    // Activer/Desactiver Promotion
    this.moduleService.promotion().then(data=>{
      if (Number(data['promotion']) === 0){
         this.paramPromo = false;
       } else {
         this.paramPromo = true;
       }
     })

    // Activer/Desactiver looks
    this.moduleService.looks().then(data=>{
      if (Number(data['visLooks']) === 0){
         this.paramLook = false;
       } else {
         this.paramLook = true;
       }
     })

     this.moduleService.enMaintenance().then(
      (data:boolean)=>{
        this.maintenance=data;
      })
  }


  onRecupPoints(){
    this.informationService.recupPoints(sessionStorage.getItem("loginCompte")).then(
      ()=>{
      }
    )
  }


  returnRepresentant(){
    this.router.navigate(['/representant']);
    sessionStorage.removeItem('isRefresh');
  }

  getApprouveur(){ // Permet de changer l'affichage selon si c'est un approuveur
    if(sessionStorage.getItem("approuveur") == 'true'){
      return true;
    } else {
      return false;
    }
  }

  getRepresentant(){ // Permet de changer l'affichage selon si c'est un representant
  if(sessionStorage.getItem("representant") == 'true'){
    return true;
  } else {
    return false;
  }
}

  getAuthStatut(){ //méthode permettant de connaitre l'état de la connexion, modifie le menu en conséquence
    if(sessionStorage.getItem("isLoggedIn")=='true'){
      return true;
    }else{
      return false;
    }
  }

  //Lors d'un clic sur power-off appel de la méthode logOut de auth.service.ts
  deconnexion(){
    this.authService.logOut();
    this.router.navigate(['/connexion']); //redirection vers connexion
  }

  //Méthode permettant de fermer le menu sur appareil mobile ou écran de petite taille
  closeMenu(type:string){
    if(type=='promo'){
      this.router.navigate(['/contenu/promo']);
    }
    if(type=='looks'){
      this.router.navigate(['/contenu/looks']);
    }
    if(type=='accueil'){
      this.router.navigate(['/contenu/accueil']);
    }
    if(type=='telechargements'){
      this.router.navigate(['/contenu/telechargements']);
    }
    if(type=="panier"){
      this.router.navigate(['/contenu/panier']);
    }
    if(type=='points'){
      this.router.navigate(['/contenu/points']);
    }
    if(type=='compte'){
      this.router.navigate(['/contenu/compte']);
    }
    if(type=='produits'){
      this.router.navigate(['/contenu/produits']);
    }
  }

    // Gere les affichages spécifiques (selon société)
  handleSpe(nomRubrique:string) {
    nomRubrique = nomRubrique.toLowerCase();
    let infoClient = JSON.parse(sessionStorage.getItem("infoClient"));
    if(typeof(infoClient) !== 'undefined'){
      switch(this.societe) {
        // Spé INWITEX
      case 'INWITEX':
        if(infoClient.civilite === 'M') {
          if(nomRubrique === 'Femme') {
            return false;
          } else {
            return true;
          }
        } else if (infoClient.civilite === 'F') {
          if(nomRubrique === 'Homme') {
            return false;
          } else {
            return true;
          }
        }
        break;

        default:
          return false;
        break;
      }
    }
    return true;
  }

  ngOnDestroy(){

  }

  searchInput(recherche){
    this.recherche = "#"+recherche.toUpperCase();
    this.router.navigate(['/contenu/produits/',this.recherche]);
  }

  useLanguage(language: string) {
      this.translate.use(language);

      if (language === "fr"){
        this.choix = 1;
      } else {
        this.choix = 2;
      }
      this.langueService.changeLangue(this.choix);
  }

  redirectLink(niveau1,niveau2,niveau3,niveau4,niveau5,niveau6,niveau7){
    let compteur  = 0;
    let monLien   = "";
    niveau1 === undefined ? compteur += 0 : compteur++ ;
    niveau2 === undefined ? compteur += 0 : compteur++ ;
    niveau3 === undefined ? compteur += 0 : compteur++ ;
    niveau4 === undefined ? compteur += 0 : compteur++ ;
    niveau5 === undefined ? compteur += 0 : compteur++ ;
    niveau6 === undefined ? compteur += 0 : compteur++ ;
    if(niveau1 === "Promo"){
      monLien = "/contenu/promo/"
      for(let i = 2; i <= compteur; i++) {
        monLien += eval("niveau" + i) + "&&"
      }

    } else {
      monLien = "/contenu/produits/"
      for(let i = 1; i <= compteur; i++) {
        monLien += eval("niveau" + i) + "&&"
      }
    }
    if(niveau1 !== "Promo"){
      monLien = monLien.slice(0, -2);
    }
    this.resetSearchBar();
    this.router.navigate([monLien]);
  }

  resetSearchBar() {
    let sb1 = (<HTMLInputElement>document.getElementById("navbar-search-input"));
    let sb2 = (<HTMLInputElement>document.getElementById("navbar-search-input2"));
    if(sb1 !== null) {
      sb1.value = "";
    } else if (sb2 !== null) {
      sb2.value = "";
    }
  }

}
