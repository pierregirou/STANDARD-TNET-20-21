import { Component, OnInit, OnDestroy,HostListener, Input, NgZone } from '@angular/core';
import { Produits } from '../../../models/produits.model';
import { BreakpointObserver } from '@angular/cdk/layout';
import { ProduitService } from '../../../services/produits.service';
import { Subscription } from 'rxjs';
import { DetailService } from '../../../services/detail-produit.service';
import { NgForm } from '@angular/forms';
import { ModuleService } from "../../../services/modules.service";
import { CommandeService } from '../../../services/commandes.service';
import { ImageService } from '../../../services/images.service';
import { isDefined } from '@angular/compiler/src/util';
import { MatSnackBar } from '@angular/material/snack-bar';
import { ActivatedRoute } from '@angular/router';
import { SelectionMenuService } from '../../../services/selection-menu.service';
import { LangueService } from '../../../services/langue.service';
import { TranslateService,LangChangeEvent } from '@ngx-translate/core';
import { FiltreService } from '../../../services/filtre.service';
import { MenuService } from '../../../services/menu.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-contenu-produits-desktop',
  templateUrl: './contenu-produits-desktop.component.html',
  styleUrls: ['./contenu-produits-desktop.component.css']
})


export class ContenuProduitsDesktopComponent implements OnInit, OnDestroy {

  deniedColor: string[] =[];
  isDesktop: boolean;
  isAuth: boolean;
  produits: Produits[] =[];
  visGalerie: boolean;
  tailleProduit: number; // permet de connaitre le nombre de produits du model Produits
  produitSubscription: Subscription;
  affichage: number =300; // variable pour récupèrer les 12 premiers produits infinite scroll
  i: number = 0;
  indice1: number = 0;
  indice2: number = 4;
  produitsTab: Produits[] =[]; // array produits spécifique pour tablette tactile
  produitTabTaille: number; // permet de connaitre le nombre de produits dans l'array et d'adapter la mise en page dans le template
  nextLimit: number; // permet de savoir quand désactiver le bouton next
  coProduit: string = ""; // permet de connaitre le libelle du produit dans la modal commande express
  coPrix: number;
  coImage: string = ""; // permet de connaitre l'image de produit dans commande express
  tailleEcran: number =1; // Variable permettant de connaitre le nombre de produits à afficher
  tailleArray: any[]; // permet de connaître le nombre de coloris par produit
  tailleP: any[]; // permet de connaitre les tailles disponibles par produit
  detailProduit: any[]; // array contenant les détails du produit
  quantite: number =0; // quantité détail produit en fonction de la taille
  prix: number =0; // prix détail produit en fonction de la taille
  nbColori: number; // permet de connaitre le nombre de colori pour chaque produit
  arrayColori: any[]; // permet de faie une bou=cle dans le template en fonction du nombre de colori présent
  tailleT:any[]; // tableau de taille des produits;
  tailleDisponible:string=" ";
  detailP:any[];
  arrayTaille:any[]=[];
  commandeExpress:boolean=false;
  commandeExpressSubscription:Subscription;
  coRefProduit:string;
  testScrollDown:boolean=false;
  mode:string; //permet de définir dans la page produit si on est en mode tableau ou ligne
  afficherFiltres:boolean;
  bRechargePage:boolean=true;
  filtreActive:boolean=false;
  precedenteSelection:string='';
  produitPromo:number;
  maLangue:string;
  langueSelect:any;
  langueSelected:number=1;
  langueSelectSubscription:Subscription;
  imageNotFound:string='';

  aucunArticle:string;
  connexionObligatoire:string;

  LibelleConst: string;
  lcLibelle1: boolean;
  lcLibelle2: boolean;
  lcTheme: boolean;

  @Input() recherche:string;

  /*
  taille1 --> col-6 col-sm-3 = 4 produits par ligne
  taille2 --> col-6 col-sm-4 = 3 produits par ligne
  taille3 --> col-6 col-sm-6 = 2 produits par ligne
  taille4 --> col-6 neutre = 1 produit par ligne
  */
 constructor(private selectionmenuService:SelectionMenuService, private filtreService:FiltreService,private route:ActivatedRoute,private snackBar:MatSnackBar, private imageService:ImageService,private router:Router, private commandeService:CommandeService,private moduleService:ModuleService, private produitService:ProduitService,private breakPoint:BreakpointObserver,private detailService:DetailService, private langueService:LangueService,translate: TranslateService, private menuService:MenuService) {

  translate.get('produit.aucun').subscribe((res: string) => {
    this.aucunArticle = res;
  });
  translate.get('connexion.obligatoire').subscribe((res: string) => {
    this.connexionObligatoire = res;
  });

  translate.onLangChange.subscribe((event: LangChangeEvent) => {
    translate.get('produit.aucun').subscribe((res: string) => {
      this.aucunArticle = res;
    });
    translate.get('connexion.obligatoire').subscribe((res: string) => {
      this.connexionObligatoire = res;
    });
  });


  var myLangue = this.langueService.getLangue();
  if(myLangue === 'FRA') {
    this.maLangue = "FRA"
  } else if(myLangue === 'ANG') {
    this.maLangue = "ANG"
  }

  this.produitService.recupProduit(this.maLangue); //appel de la méthode recupProduit
  this.commandeService.initCommande(); //initialise une nouvelle commande une fois sur la page des produits

  //Utilisation de breakpoint pour détecter un changement de la taille de l'écran en largeur
  breakPoint.observe([
    '(max-width: 1288px)'
  ]).subscribe(result => {
    if (result.matches) {
      this.tailleEcran=2; //si atteint 1288px passe la taille à 2 --> 3 produits
    }else{
      this.tailleEcran=1; //sinon revient à une taille à 1 --> 4 produits
    }
  });
  breakPoint.observe([
    '(max-width: 785px)'
  ]).subscribe(result => {
    if (result.matches) {
      this.tailleEcran=3; //si atteint 785px passe la taille à 3 --> 2 produits
    }else{
      if(Number(window.innerWidth)<1288){
        this.tailleEcran=2; //sinon si la taille est inférieure à 1288px revient à une taille à 2 --> 3 produits
      }
    }
  });
  breakPoint.observe([
    '(max-width: 550px)'
  ]).subscribe(result => {
    if (result.matches) {
      this.tailleEcran=4; //si atteint 550 px passe la taille à 4 --> 1 produit
    }else{
      if(Number(window.innerWidth)<1000){
        this.tailleEcran=3 //sinon si la taille est inférieure à 785px revient à une taille à 3 --> 2 produits
      }
    }
  });

}

ngOnInit() {

  this.imageNotFound = this.imageService.PhotosArt + '/../../Images/no_image.png';

  var myLangue = this.langueService.getLangue();
  if(myLangue === 'FRA') {
    this.maLangue = "FRA"
  } else if(myLangue === 'ANG') {
    this.maLangue = "ANG"
  }

  this.langueSelected=this.langueService.langueSelect;
  this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(langue=>{
    this.langueSelect=langue;

    if(String(langue) === '1') {
      this.maLangue = "FRA"

    } else if(String(langue) === '2') {
      this.maLangue = "ANG"
    }
    this.route.params.subscribe(
      (value)=>{
        //this.precedenteSelection = '';
        this.selectionmenuService.getSelectionProduits(this.precedenteSelection,this.maLangue).then(
          (produitSelection:Produits[])=>{
            this.produits=produitSelection;
            this.tailleProduit=this.produits.length;
          }
        );
      });
      this.produitService.emitProduits();
  })

    //Activer/Désactiver les informations à afficher
    this.moduleService.libelleConstruct().then(data => {
      this.LibelleConst = data['libelleConstruct'];
      var valLib1 = "1";
      var valLib2 = "2";
      var valTheme = "3";

      this.LibelleConst.search(valLib1) == -1  ? this.lcLibelle1 = false  : this.lcLibelle1 = true
      this.LibelleConst.search(valLib2) == -1  ? this.lcLibelle2 = false  : this.lcLibelle2 = true
      this.LibelleConst.search(valTheme) == -1 ? this.lcTheme = false     : this.lcTheme = true


    })

  this.menuService.initialiseMenu();



  if(sessionStorage.getItem("isLoggedIn")=="true"){
    this.isAuth=true;
  }else{
    this.isAuth=false
  }


  //détermine le mode d'affichage des produits
  this.moduleService.modeSaisie().then(
    (data:string)=>{
      if(data==="1"){
        this.mode="ligne";
      }
      if(data==="2"){
        this.mode="tableau";
      }
    }
  )
    // determine si on affiche les filtres
  this.moduleService.getAfficherFiltres().then(
    (data:boolean)=>{
        this.afficherFiltres=data;
    }
  )
  this.route.params.subscribe(
    (value)=>{
      if (this.precedenteSelection !== value["selection"]) {
        this.filtreActive = false;
      }
      if(isDefined(value["selection"]) && value["selection"].substring(0,1) === "#" && value["selection"].length > 0){
        if (value["selection"].length > 1) {
          this.produitSubscription=this.produitService.produitSubject.subscribe(
            (produit:Produits[])=>{
              this.recherche=value["selection"].substring(1);
              this.produits=produit.slice(0);
              /*for(let p=0; p < this.produits.length; p++) {
               var marecherche =  this.produits.find(element => element.refproduit === this.recherche)
              } */
              this.tailleProduit=this.produits.length +1;
              this.nextLimit=Math.trunc((produit.length)/6);
            });
            this.produitService.emitProduits(); //renvoi les infos du périphérique utilisé
        } else {
          this.produitSubscription=this.produitService.produitSubject.subscribe(
            (produit:Produits[])=>{
              this.recherche="";
              this.produits=produit.slice(0,this.affichage);
              this.tailleProduit=this.produits.length +1;
              this.nextLimit=Math.trunc((produit.length)/6);
            });
            this.produitService.emitProduits(); //renvoi les infos du périphérique utilisé
            this.isDesktop=this.produitService.isDesktop; //renvoi true si Desktop
        }


      } else if(isDefined(value["selection"]) && value["selection"].substring(0,1) === "$" && value["selection"].length > 1){
        //
        //dans le cas ou on applique un filtre
        //
            var valueFiltrer = value["selection"];
            this.recherche="";
            this.selectionmenuService.getSelectionProduits(valueFiltrer,this.maLangue).then(
              (produitSelection:Produits[])=>{
                this.produits=produitSelection.slice(0,this.affichage);
                this.tailleProduit=this.produits.length;
              }
            );
         } else if (isDefined(value["selection"]) && !this.filtreActive){
        this.recherche="";
        this.precedenteSelection = value["selection"];
        this.selectionmenuService.getSelectionProduits(this.precedenteSelection,this.maLangue).then(
          (produitSelection:Produits[])=>{
            if(produitSelection.length+1===0){
              this.snackBar.open(this.aucunArticle,"",{
                duration:3000
              });
            }
            this.produits=produitSelection.slice(0,this.affichage);
            this.tailleProduit=this.produits.length;
          }
        );
      }else{
        //initialisation des produits pc
        this.produitSubscription=this.produitService.produitSubject.subscribe(
          (produit:Produits[])=>{
            this.recherche="";
            this.produits=produit.slice(0,this.affichage);
            this.tailleProduit=this.produits.length +1;
            this.nextLimit=Math.trunc((produit.length)/6);
          });
          this.produitService.emitProduits(); //renvoi les infos du périphérique utilisé
          this.isDesktop=this.produitService.isDesktop; //renvoi true si Desktop
      }
    }
  );

}

//permet de connaitre la position du scroll
@HostListener('window:scroll', ['$event'])
  checkScroll() {
    const scrollPosition = window.pageYOffset
    if (scrollPosition >= 1000 && this.isDesktop===true && window.innerWidth>1000) {
      document.getElementById("cRetour").className="cVisible"; //si à += 1000px du haut de page affiche le bouton pour revenir en haut
      //Lorsque l'utilisateur descend met les modules en position fixed
      var modules = document.getElementById("modules");
      modules.style.position="fixed";
      modules.style.marginRight="0%";
      modules.style.marginLeft="80%";
      modules.style.marginTop="-10%";
      /**********************************************/
    }else if(this.isDesktop===true) {
      document.getElementById("cRetour").className="cInvisible"; //Si < 1000px cache le bouton pour revenir en haut de la page
    }

    if(scrollPosition<=100 && this.isDesktop===true && window.innerWidth>1000){
      //Lorsque l'utilisateur arrive à une position proche du haut de la page remet les modules en poisition relative
      var modules = document.getElementById("modules");
      modules.style.position="relative";
      modules.style.marginRight="2.5%";
      modules.style.marginLeft="0%";
      modules.style.marginTop="0px";
      /***********************************************/
    }

}



scrollDown(){ //infinite scroll lorsque l'utilisateur scroll en bas affiche le reste des produits 4*3 12 par 12
  this.affichage+=12;
  this.testScrollDown=true;
  var modules = document.getElementById("modules");
  if(window.innerWidth>1000){
    modules.style.position="fixed";
    modules.style.marginRight="0px";
    modules.style.marginLeft="80%";
    modules.style.marginTop="-10%";
  }

  this.route.params.subscribe(
    (value)=>{
      //this.produits=[];
      if(isDefined(value["selection"]) && value["selection"].substring(0,1) === "#" && value["selection"].length > 0){
        this.produitSubscription=this.produitService.produitSubject.subscribe(
          (produit:Produits[])=>{
            //
            //dans le cas ou on recherche dans le menu
            //


        if (value["selection"].length > 1) {
          this.produitSubscription=this.produitService.produitSubject.subscribe(
            (produit:Produits[])=>{
              this.recherche=value["selection"].substring(1);
              this.produits=produit.slice(0,this.affichage); //affiche le premier élement jusqu'au 12eme par defaut et rajouter 12 à chaque scroll down
              this.tailleProduit=this.produits.length;
            }
          );
          this.produitService.emitProduits();
          window.top.window.scrollTo(0,0);
        } else {
          this.recherche="";
          var selection = value.selection;
          var tab=selection.split('&&');
          this.selectionmenuService.getSelectionProduits(selection,this.maLangue).then(
            (produitSelection:Produits[])=>{
              this.produits=produitSelection.slice(0,this.affichage);
              this.tailleProduit=this.produits.length;
            }
          );
        }
      });
      } else if(isDefined(value["selection"]) && value["selection"].substring(0,1) === "$" && value["selection"].length > 1){
        //
        //dans le cas ou on applique un filtre
        //
            var valueFiltrer = value["selection"];
            this.recherche="";
            this.selectionmenuService.getSelectionProduits(valueFiltrer,this.maLangue).then(
              (produitSelection:Produits[])=>{
                this.produits=produitSelection.slice(0,this.affichage);
                this.tailleProduit=this.produits.length;
              }
            );
      }else if(isDefined(value["selection"]) && !this.filtreActive){
        //
        //dans le cas ou on séléctionne à partir du menu
        //

        this.recherche="";
        var selection = value.selection;
        var tab=selection.split('&&');
        this.selectionmenuService.getSelectionProduits(selection,this.maLangue).then(
          (produitSelection:Produits[])=>{
            this.produits=produitSelection.slice(0,this.affichage);
            this.tailleProduit=this.produits.length;
          }
        );
      }else{

         //
        //dans le cas ou on affiche tous les produits
        //
        this.produitSubscription=this.produitService.produitSubject.subscribe(
          (produit:Produits[])=>{
            this.recherche="";
            this.produits=produit.slice(0,this.affichage); //affiche le premier élement jusqu'au 12eme par defaut et rajouter 12 à chaque scroll down
            this.tailleProduit=this.produits.length;
          }
        );
        this.produitService.emitProduits();
      }
    }
  );
  if(window.pageYOffset>1000 && this.isDesktop===true){
    document.getElementById("cRetour").className="cVisible";
  }
}

  onCommande(refproduit:string,libelle:string,saison:string,codeColoris:string,promo:any,prix:number=0.0){ //fonction permettant de détecter sur quel produit est sélectionné pour la commande expresse
    //prend en paramètre le libelle en string du produit
    this.produitPromo=promo;
    this.commandeService.setArticlePromo(promo);
    this.coRefProduit="";
    this.coProduit=libelle; //coProduit prend la valeur du produit passé en paramètre de la fonction
    this.commandeExpress=true;
    this.coRefProduit=refproduit;
    this.coPrix=prix;
    this.commandeService.coRefProduit=refproduit;

    this.commandeService.emitCoRef();
    if(!this.isAuth){
      this.snackBar.open(this.connexionObligatoire,"",{
        duration:3000
      });
    }
  }



  onSubmit(form:NgForm){
    for(let i=0;i<Object.keys(form.form.value).length;i++){
      const arrayForm = Object.keys(form.form.value);
      const indice = arrayForm[i];
    }
  }

  //fonction permettant de revenir en haut de la page
  hautPage(){
    window.top.window.scrollTo(0,0); //renvoi en haut
    document.getElementById('cRetour').className="cInvisible"; //une fois en haut cache le bouton
  }

  goDetail(promo:string, refproduit:string, tarifpromo:string, codeColori:any, libcolori:string="") {
    sessionStorage.setItem('codeColoriDetailProduit',codeColori);
    sessionStorage.setItem('libColoriDetailProduit',libcolori);
    sessionStorage.setItem('refproduitPourDetailProduit',refproduit);
    promo==='1'?this.router.navigate(['/detail-produit/',refproduit,tarifpromo]):this.router.navigate(['/detail-produit/',refproduit]);
  }

  onChangementFiltre(tabCouleur) {
    if (tabCouleur.length !== 0) {
      this.filtreActive = true;
      this.produitSubscription=this.produitService.produitSubject.subscribe(
        (produit:Produits[])=>{
          this.produits=produit.slice(0,this.affichage); //affiche le premier élement jusqu'au 12eme par defaut et rajouter 12 à chaque scroll down
          this.tailleProduit=this.produits.length;
        }
      );
      this.produitService.emitProduits();
    } else {
      this.filtreActive = false;
    }
  }

  changeImagePres(codeColori:string, i:number) {
    if (this.produits[i].image === this.imageNotFound && codeColori === 'notfound') return;
     // `Baseurl/Photos/CodesaisonRefproduit-Codecolori-1.jpg`
    let produitsRefproduit = this.produits[i].refproduit;
    // produitsRefproduit.replace(' ', '_');   PAS POUR AMATEIS
    // let nouvelUrl = `${this.imageService.PhotosArt}/Photos/${this.produits[i].saison}${produitsRefproduit}.jpg`;
    // let nouvelUrl = `${this.imageService.PhotosArt}/Photos/${this.produits[i].saison}${this.produits[i].refproduit}.jpg`;
    let nouvelUrl = `${this.imageService.PhotosArt}/Photos/${this.produits[i].saison}${this.produits[i].refproduit}-${codeColori}-1.jpg`;
    this.produits[i].image = codeColori !== 'notfound' ? nouvelUrl : this.imageNotFound;
  }

  ngOnDestroy(){
    this.filtreService.resetFiltres();
  }
}
