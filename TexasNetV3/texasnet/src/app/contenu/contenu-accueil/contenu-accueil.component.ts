import { Component, OnInit, OnDestroy } from '@angular/core';
import { ProduitService } from '../../services/produits.service';
import { MenuService } from '../../services/menu.service';
import { Subscription } from 'rxjs';
import { Produits } from '../../models/produits.model';
import { BreakpointObserver } from '@angular/cdk/layout';
import { CommandeService } from '../../services/commandes.service';
import { ModuleService } from '../../services/modules.service';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { HtmlAstPath } from '@angular/compiler';
import { HttpRequest } from '../../services/http-request.service';
import { TemplateService } from '../../services/template.service';
import { LangueService } from '../../services/langue.service';
import { ImageService } from '../../services/images.service';


@Component({
  selector: 'app-contenu-accueil',
  templateUrl: './contenu-accueil.component.html',
  styleUrls: ['./contenu-accueil.component.css']
})
export class ContenuAccueilComponent implements OnInit, OnDestroy {
  mode:string="";
  tailleEcran:number=1;
  isDesktop:boolean;
  isMobile:boolean;
  isTablet:boolean;
  tailleProduit:number;
  portrait:boolean;
  paysage:boolean;
  /* Affiche ou non un bouton pour revenir en haut de la page */
  page:number=1;
   /* Taille du tableau des produits mobile */
  tailleTabM:number;
  /* Indice pour vue mosaique 4*4 */
  indiceM1:number=0;
  indiceM2:number=4;
  produitMob:Produits[];
  commandeExpress:boolean=false;
  coRefProduit:string="";
  coProduit:string="";
  produitPromoInfo:number;
  coPrix:number;
  paramSelection:boolean;
  maintenance:boolean;
  texteAccueil:HtmlAstPath;
  contenuColor:string;
  contenuColorSubscription:Subscription;
  texteAccueilAnglais:HtmlAstPath; //texte accueil anglais
  langueSelect:number;
  langueSelectSubscription:Subscription;
  produitSelection:Produits[]=[];
  produitSelectionSubscription:Subscription;
  produits:Produits[]=[];
  produitsSubscription:Subscription;
  codeTarif:boolean;
  promoPourcentageCodeTarif:string;
  maLangue:string;
  langueSelected:number=1;
  imageNotFound:string='';

  constructor(private langueService:LangueService,private templateService:TemplateService, private httpRequest:HttpRequest, private httpClient:HttpClient, private produitService:ProduitService, private menuService:MenuService, private BreakpointObserver: BreakpointObserver,private commandeService:CommandeService,private moduleService:ModuleService, private router:Router,private imageService:ImageService){
    //Utilisation de breakpoint pour détecter un changement de la taille de l'écran en largeur
    BreakpointObserver.observe([
      '(max-width: 1288px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.tailleEcran=2; //si atteint 1288px passe la taille à 2 --> 3 produits
      }else{
        this.tailleEcran=1; //sinon revient à une taille à 1 --> 4 produits
      }
    });
    BreakpointObserver.observe([
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
    BreakpointObserver.observe([
      '(max-width: 550px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.tailleEcran=4; //si atteint 550 px passe la taille à 4 --> 1 produit
      }else{
        if(Number(window.innerWidth)<785){
          this.tailleEcran=3 //sinon si la taille est inférieure à 785px revient à une taille à 3 --> 2 produits
        }
      }
    });

    BreakpointObserver.observe([
      //cas orientation portrait
      '(orientation: portrait)']).subscribe(result=>{
        if(result.matches){
          this.portrait=true;
          this.paysage=false;
        }else{
          //cas orientation paysage
          this.paysage=true;
          this.portrait=false
        }
    });

    // Activer/Desactiver Promotion
    this.moduleService.selectionMoment().then(data=>{
      if (Number(data['selectionMoment']) === 0){
         this.paramSelection = false;
       } else {
         this.paramSelection = true;
       }
     })

    // Mode
    this.moduleService.modeSaisie().then(data=>{
      if (data['modeSaisie'] === 1){
         this.mode = "ligne";
       } else {
         this.mode = "tableau";
       }
     })
  }

  ngOnInit() {


    
    this.menuService.initialiseMenu();

    this.imageNotFound = this.imageService.PhotosArt + '/../../Images/no_image.png';

    this.imageNotFound = this.imageService.PhotosArt + '/../../Images/no_image.png';

    var myLangue = this.langueService.getLangue();
    if(myLangue === 'FRA') {
      this.maLangue = "FRA"
    } else if(myLangue === 'ANG') {
      this.maLangue = "ANG"
    }

    this.produitsSubscription=this.produitService.produitSubject.subscribe(
      (produit:Produits[])=>{
        this.produits = produit;
      }
    );

    this.langueSelected=this.langueService.langueSelect;
    this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(langue=>{
      this.langueSelect=langue;
      if(String(langue) === '1') {
        this.maLangue = "FRA"
      } else if(String(langue) === '2') {
        this.maLangue = "ANG"
      }      
      this.produitService.recupSelection(this.maLangue);
    })

    if(sessionStorage.getItem("codeTarifClient")==="true"){
      this.codeTarif=true;
      this.promoPourcentageCodeTarif=sessionStorage.getItem("promoPourcentageCodeTarif");
    }else{
      this.codeTarif=false;
    }

    this.langueSelect=this.langueService.langueSelect;
    this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(langue=>{
      this.langueSelect=langue;
    })


    this.templateService.getContenuColor();
    this.contenuColorSubscription=this.templateService.contenuColorSubject.subscribe(
      (contenuColor:string)=>{
        this.contenuColor='#'+contenuColor;
      }
    );
    this.templateService.emitContenuColor();

    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoTexte"
    }).subscribe(data=>{
      this.texteAccueil=data[0];
    });

    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoTexteAng"
    }).subscribe(data=>{
      this.texteAccueilAnglais=data[0];
    })


    this.moduleService.enMaintenance().then(
      (data:boolean)=>{
        this.maintenance=data;
      })

    this.isDesktop=this.produitService.isDesktop;
    this.isMobile=this.produitService.isMobile;
    this.isTablet=this.produitService.isTablet;
    this.produitSelectionSubscription=this.produitService.ProduitSelectionSubject.subscribe(
      (produitSelection:Produits[])=>{
        this.produitSelection=produitSelection;
        this.tailleProduit=produitSelection.length;
        this.produitMob=produitSelection.slice(this.indiceM1,this.indiceM2); //récupère les produits 4*4
        var keys = Object.keys(this.produitMob); //transforme l'objet en array pour en connaître la taille
        this.tailleTabM=keys.length;
      }
    );
    this.produitService.recupSelection(this.maLangue);
    this.produitService.recupProduit(this.maLangue);
    this.produitService.emitProduitSelection();

  }

  //Méthode appelée avec la flèche previous
  goPrevious(){
    //actualise les indices en enlevant 4 produits à chaque fois si mosaique
    this.indiceM1-=4;
    this.indiceM2-=4;
    this.initProduitMob(); //actualise produitMob
    this.page--;
  }

  //Méthode appelée avec la flèche next
  goNext(){
    //actualise les indices en ajoutant 4 produits à chaque fois si mosaique
    this.indiceM1+=4;
    this.indiceM2+=4;
    this.initProduitMob(); //actualise produitMob

    this.page++;
  }

  /* Méthode pour récupérer les produits spécialement pour mobile */
  initProduitMob(){
    this.produitSelectionSubscription=this.produitService.ProduitSelectionSubject.subscribe(
      (produit:Produits[])=>{
        this.produitMob=produit.slice(this.indiceM1,this.indiceM2); //récupère les produits 4*4
        var keys = Object.keys(this.produitMob); //transforme l'objet en array pour en connaître la taille
        this.tailleTabM=keys.length;
      }
    );
    this.produitService.emitProduitSelection(); //demande au service produit d'émettre les produits dans l'appli
  }

  /*onCommande(refproduit:string,libelle:string){ //fonction permettant de détecter sur quel produit est sélectionné pour la commande expresse
    //prend en paramètre le libelle en string du produit
    this.coRefProduit="";
    this.coProduit=libelle; //coProduit prend la valeur du produit passé en paramètre de la fonction
    this.commandeExpress=true;

    this.coRefProduit=refproduit;
    this.commandeService.coRefProduit=refproduit;
    this.commandeService.emitCoRef();
  }*/


  onCommande(refproduit:string,libelle:string,saison:string,codeColoris:string,promo:any,prix:number=0.0){ //fonction permettant de détecter sur quel produit est sélectionné pour la commande expresse
    //prend en paramètre le libelle en string du produit
    this.produitPromoInfo=promo;
    this.commandeService.setArticlePromo(promo);
    this.coRefProduit="";
    this.coProduit=libelle; //coProduit prend la valeur du produit passé en paramètre de la fonction
    this.commandeExpress=true;
    this.coRefProduit=refproduit;
    this.coPrix=prix;
    this.commandeService.coRefProduit=refproduit;
    this.commandeService.emitCoRef();
  }

  goDetail(promo:string, refproduit:string, tarifpromo:string, codeColori:string, libcolori:string="") {
    sessionStorage.setItem('codeColoriDetailProduit',codeColori);
    sessionStorage.setItem('libColoriDetailProduit',libcolori);
    sessionStorage.setItem('refproduitPourDetailProduit',refproduit);
    promo==='1'?this.router.navigate(['/detail-produit/',refproduit,tarifpromo], {queryParams:{selec:true}}):this.router.navigate(['/detail-produit/',refproduit], {queryParams:{selec:true}});
  }

  changeImagePres(codeColori:string, i:number) {
    if (this.produitSelection[i].image === this.imageNotFound && codeColori === 'notfound') return;
                  // `Baseurl/Photos/CodesaisonRefproduit-Codecolori-1.jpg`
    let nouvelUrl = `${this.imageService.PhotosArt}/Photos/${this.produitSelection[i].saison}${this.produitSelection[i].refproduit}-${codeColori}-1.jpg`;
    this.produitSelection[i].image = codeColori !== 'notfound' ? nouvelUrl : this.imageNotFound;
  }

  ngOnDestroy(){
    this.produitSelectionSubscription.unsubscribe();
  }
}
