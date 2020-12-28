import { Component, OnInit, OnDestroy } from '@angular/core';
import { ProduitService } from '../../services/produits.service';
import { Subscription } from 'rxjs';
import { Produits } from '../../models/produits.model';
import { BreakpointObserver } from '@angular/cdk/layout';
import { CommandeService } from '../../services/commandes.service';
import { TemplateService } from '../../services/template.service';
import { ActivatedRoute } from '@angular/router';
import { SelectionMenuService } from '../../services/selection-menu.service';
import { ImageService } from '../../services/images.service';
import { MatSnackBar } from '@angular/material';
import { isDefined } from '@angular/compiler/src/util';
import { ModuleService } from '../../services/modules.service';
import { LangueService } from '../../services/langue.service';
import { Router } from '@angular/router';
import { TranslateService,LangChangeEvent } from '@ngx-translate/core';

@Component({
  selector: 'app-contenu-promo',
  templateUrl: './contenu-promo.component.html',
  styleUrls: ['./contenu-promo.component.css']
})
export class ContenuPromoComponent implements OnInit, OnDestroy {

  tailleEcran:number=1;
  tailleProduit:number;
  commandeExpress:boolean=false;
  coRefProduit:string="";
  coProduit:string="";
  coPrix:number;
  produitPromo:Produits[];
  produitPromoSubscription:Subscription;
  produitPromoInfo:number;
  mode:string;
  isDesktop:boolean;
  isMobile:boolean;
  isTablet:boolean;
  contenuColor:String;
  contenuColorSubscription:Subscription;
  recherche:string;
  maLangue:string;
  langueSelect:any;
  langueSelected:number=1;
  langueSelectSubscription:Subscription;
  aucunArticle:string;
  imageNotFound:string='';

  constructor(private moduleService:ModuleService,private snackBar:MatSnackBar,private route:ActivatedRoute,private selectionMenuService:SelectionMenuService, private imageService:ImageService,private templateService:TemplateService,private produitService:ProduitService, private BreakpointObserver:BreakpointObserver, private router:Router, private commandeService:CommandeService, private langueService:LangueService,translate: TranslateService) {

    translate.get('produit.aucun').subscribe((res: string) => {
      this.aucunArticle = res;
    });

    translate.onLangChange.subscribe((event: LangChangeEvent) => {
      translate.get('produit.aucun').subscribe((res: string) => {
        this.aucunArticle = res;
      });
    });

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
      
      this.produitService.recupPromo(this.maLangue);
      /*this.route.params.subscribe(
        (value)=>{
          this.produitService.recupPromo(this.maLangue);
          if(isDefined(value["selection2"])){
            var selection = value["selection2"];
            this.selectionMenuService.getSelectionPromo(selection,this.maLangue).then(
              (produitSelection:Produits[])=>{
                this.produitPromo=produitSelection
                this.tailleProduit=this.produitPromo.length;
              }
            );
            
          }
          
          this.produitService.emitProduitPromo();
        }
      );*/
      
    })

    this.produitService.recupPromo(this.maLangue);
    //détermine le mode d'affichage des produits
  this.moduleService.modeSaisie().then(
    (data:number)=>{
      if(data===1){
        this.mode="ligne";
      } else {
        this.mode="tableau";
      }
    }
  );

    this.route.params.subscribe(
      (value)=>{
        this.produitService.recupProduit("FRA");
        if(isDefined(value["selection2"])){
          var selection = value["selection2"];
          this.selectionMenuService.getSelectionPromo(selection,"FRA").then(
            (produitSelection:Produits[])=>{
              if(produitSelection.length===0){
                this.snackBar.open(this.aucunArticle,"",{
                  duration:3000
                });
              }
              this.produitPromo=produitSelection
              this.tailleProduit=this.produitPromo.length;
            }
          );
        }else{
          //initialisation des produits promo pc
          this.produitPromoSubscription=this.produitService.produitPromoSubject.subscribe(
            (produitPromo:Produits[])=>{
              this.produitPromo=produitPromo;
              this.tailleProduit=produitPromo.length;
            }
          );
          this.produitService.emitProduitPromo();
        }
      }
    );

    this.templateService.getContenuColor();
    this.contenuColorSubscription=this.templateService.contenuColorSubject.subscribe(
      (contenuColor:string)=>{
        this.contenuColor='#'+contenuColor;
      }
    );
    this.templateService.emitContenuColor();
    this.isDesktop=this.produitService.isDesktop;
    this.isMobile=this.produitService.isMobile;
    this.isTablet=this.produitService.isTablet;
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

  /*onCommande(refproduit:string,libelle:string,saison:string,codeColoris:string,promo:any,prix:number=0.0){ //fonction permettant de détecter sur quel produit est sélectionné pour la commande expresse
    //prend en paramètre le libelle en string du produit
    this.produitPromoInfo=promo;
    this.commandeService.setArticlePromo(promo);
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


  goDetail(promo:string, refproduit:string, tarifpromo:string, codeColori:any, libcolori:string="") {
    sessionStorage.setItem('codeColoriDetailProduit',codeColori);
    sessionStorage.setItem('libColoriDetailProduit',libcolori);
    sessionStorage.setItem('refproduitPourDetailProduit',refproduit);
    promo==='1'?this.router.navigate(['/detail-produit/',refproduit,tarifpromo]):this.router.navigate(['/detail-produit/',refproduit]);
  }


  searchInput(value){ //détecte la saisie dans la barre de recherche et envoie au service la valeur entrée en input
    this.recherche = value.toUpperCase();
  }

  changeImagePres(codeColori:string, i:number) {
    if (this.produitPromo[i].image === this.imageNotFound && codeColori === 'notfound') return;
                  // `Baseurl/Photos/CodesaisonRefproduit-Codecolori-1.jpg`
    let nouvelUrl = `${this.imageService.PhotosArt}/Photos/${this.produitPromo[i].saison}${this.produitPromo[i].refproduit}-${codeColori}-1.jpg`;
    this.produitPromo[i].image = codeColori !== 'notfound' ? nouvelUrl : this.imageNotFound;
  }

  ngOnDestroy(){
    /*this.route.params.subscribe(
      (value)=>{
        this.produitService.recupProduit();
        if(isUndefined(value["selection2"])){
          this.produitPromoSubscription.unsubscribe();
        }
      });*/
    }
}
