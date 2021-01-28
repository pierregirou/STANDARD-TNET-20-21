import { Component, OnInit, Output, OnDestroy, EventEmitter } from '@angular/core';
import { ProduitService } from "../services/produits.service";
import { Subscription } from "rxjs";
import { Colori } from '../models/coloris.models';
import { ActivatedRoute } from '@angular/router';
import { isUndefined } from 'util';
import { Router } from '@angular/router';
import { FiltreService } from '../services/filtre.service';
import { LangueService } from '../services/langue.service';

@Component({
  selector: 'app-filtre-box',
  templateUrl: './filtre-box.component.html',
  styleUrls: ['./filtre-box.component.css']
})
export class FiltreBoxComponent implements OnInit {
  ligneFiltre:any[]=[];
  ligneFiltreSubscription:Subscription;
  familleFiltre:any[]=[];
  familleFiltreSubscription:Subscription;
  themeFiltre:any[]=[];
  themeFiltreSubscription:Subscription;
  coloriFiltre:any[]=[];
  coloriFiltreSubscription:Subscription;
  tailleFiltre:any[]=[];
  tailleFiltreSubscription:Subscription;
  matiereFiltre:any[]=[];
  matiereFiltreSubscription:Subscription;
  marqueFiltre:any[]=[];
  marqueFiltreSubscription:Subscription;
  sousFamilleFiltre:any[]=[];
  sousFamilleFiltreSubscription:Subscription;
  modeleFiltre:any[]=[];
  modeleFiltreSubscription:Subscription;

  ligneProduit:Subscription;
  familleProduit:Subscription;
  themeProduit:Subscription;
  coloriProduit:Subscription;
  tailleProduit:Subscription;
  matiereProduit:Subscription;
  sousFamilleProduit:Subscription;
  marqueProduit: Subscription;
  modeleProduit:Subscription;

  ligneSelect:string="";
  familleSelect:string="";
  themeSelect:string="";
  coloriSelect:string="";
  matiereSelect:string="";
  tailleSelect:string="";
  sousFamilleSelect:string="";
  marqueSelect: string="";
  modeleSelect: string="";

  langueSelect:any;
  langueSelected:number=1;
  langueSelectSubscription:Subscription;
  themeSelected: string;

  constructor(private produitService:ProduitService, private router:Router,private route:ActivatedRoute, private filtreService:FiltreService, private langueService:LangueService){

    this.myLangue();

    this.chargeFiltres();

    this.langueSelected=this.langueService.langueSelect;
    this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(langue=>{
      this.langueSelect=langue;
      if(String(langue) === '1') {
        this.langueSelect = "FRA";
      } else if(String(langue) === '2') {
        this.langueSelect = "ANG";
      }
      this.chargeFiltres();
      this.getElementAndReturnNewArrayInFilter()

    })
  }

  ngOnInit() {
    this.chargeFiltres();

    this.ligneProduit = this.filtreService.ligneSubject.subscribe(
      (ligne:string) =>{
        if (this.ligneFiltre !== undefined && this.ligneFiltre.length > 0) {
          if (this.ligneFiltre.indexOf(ligne) !== -1) this.ligneSelect = ligne;
        }
      }
    );
    this.familleProduit = this.filtreService.familleSubject.subscribe(
      (famille:string) =>{
        if (this.familleFiltre !== undefined && this.familleFiltre.length > 0) {
          if (this.familleFiltre.indexOf(famille) !== -1) this.familleSelect = famille;
        }
      }
    );
    this.sousFamilleProduit = this.filtreService.sousFamilleSubject.subscribe(
      (sousFamille:string) =>{
        if (this.sousFamilleFiltre !== undefined && this.sousFamilleFiltre.length > 0) {
          if (this.sousFamilleFiltre.indexOf(sousFamille) !== -1) this.sousFamilleSelect = sousFamille;
        }
      }
    ); this.marqueProduit = this.filtreService.marqueSubject.subscribe(
      (marque:string) =>{
        if (this.marqueFiltre !== undefined && this.marqueFiltre.length > 0) {
          if (this.marqueFiltre.indexOf(marque) !== -1) this.marqueSelect = marque;
        }
      }
    );
    this.themeProduit = this.filtreService.themeSubject.subscribe(
      (theme:string) =>{
        if (this.themeFiltre !== undefined && this.themeFiltre.length > 0) {
          if (this.themeFiltre.indexOf(theme) !== -1) this.themeSelect = theme;
        }
      }
    );
    this.coloriProduit = this.filtreService.coloriSubject.subscribe(
      (colori:any) =>{
        if (this.coloriFiltre !== undefined && this.coloriFiltre.length > 0) {
          if (this.coloriFiltre.indexOf(colori) !== -1) this.coloriSelect = colori;
        }
      }
    );
    this.matiereProduit = this.filtreService.matiereSubject.subscribe(
      (matiere:string) =>{
        if (this.matiereFiltre !== undefined && this.matiereFiltre.length > 0) {
          if (this.matiereFiltre.indexOf(matiere) !== -1) this.matiereSelect = matiere;
        }
      }
    );
    this.tailleProduit = this.filtreService.tailleSubject.subscribe(
      (taille:string) =>{
        if (this.tailleFiltre !== undefined && this.tailleFiltre.length > 0) {
          if (this.tailleFiltre.indexOf(taille) !== -1) this.tailleSelect = taille;
        }
      }
    );
    this.modeleProduit = this.filtreService.modeleSubject.subscribe(
      (modele:string) =>{
        if (this.modeleFiltre !== undefined && this.modeleFiltre.length > 0) {
          if (this.modeleFiltre.indexOf(modele) !== -1) this.modeleSelect = modele;
        }
      }
    );
    this.ligneSelect = this.filtreService.getCurrentLine();
    this.familleSelect = this.filtreService.getCurrentFamille();
    this.themeSelect = this.filtreService.getCurrentTheme();
    this.coloriSelect = this.filtreService.getCurrentColori();
    this.tailleSelect = this.filtreService.getCurrentTaille();
    this.matiereSelect = this.filtreService.getCurrentMatiere();
    this.marqueSelect = this.filtreService.getCurrentMarque();
    this.sousFamilleSelect = this.filtreService.getCurrentsousFamille();
    this.modeleSelect = this.filtreService.getCurrentModele();

  }

  chargeFiltres() {
    this.tailleFiltre=[];
    this.coloriFiltre=[];
    this.matiereFiltre=[];
    this.ligneFiltre=[];
    this.familleFiltre=[];
    this.themeFiltre=[];
    this.sousFamilleFiltre=[];
    this.marqueFiltre=[];
    this.modeleFiltre=[];

   /* Détermine toutes les tailles à filtrer */
   this.produitService.recupTailleFiltre(this.langueSelect);
   this.tailleFiltreSubscription=this.produitService.tailleFiltreSubject.subscribe(
     (taille:any[])=>{
      //FICHIER info-filtre.php  [true,{"codeGammeTaille":"001","taille":["36","38","40","42","44","46","48","50"]},{"codeGammeTaille":"000","taille":["TU"]}]
       this.tailleFiltre=[];
       for(let y = 0; y < taille.length; y++){
        for(let i = 0; i < taille[y].tailles.length; i++){
            var myValue = taille[y].tailles;
            this.tailleFiltre.push(myValue[i][0].taille);
          }
        }
       this.tailleFiltre = this.removeDuplicates(this.tailleFiltre)
       this.tailleFiltre.sort();
     }
   );

   /* Détermine toutes les coloris à filtrer */
   this.produitService.recupColorisFiltreBox(this.langueSelect);
   this.coloriFiltreSubscription=this.produitService.colorisFiltreBoxSubject.subscribe(
     (coloris:any[])=>{
      console.log(coloris);
       this.coloriFiltre=[]
       for(let i = 0; i < coloris.length; i++){
         var myValue = coloris;
         this.coloriFiltre.push(myValue[i]);
       }

       this.coloriFiltre = this.removeDuplicates(this.coloriFiltre)
       this.coloriFiltre.sort();
     }
   );

   /* Détermine toutes les matières à filtrer */
   this.produitService.recupMatiereFiltre(this.langueSelect);
   this.matiereFiltreSubscription=this.produitService.matiereFiltreBoxSubject.subscribe(
     (matiere:any[])=>{
       this.matiereFiltre=[]
       for(let i = 0; i < matiere.length; i++){
         var myValue = matiere;
         this.matiereFiltre.push(myValue[i]);
       }

       this.matiereFiltre = this.removeDuplicates(this.matiereFiltre)
       this.matiereFiltre.sort();
     }
   );


   /* Détermine toutes les lignes à filtrer */
   this.produitService.recupLigneFiltre(this.langueSelect);
   this.ligneFiltreSubscription=this.produitService.ligneFiltreBoxSubject.subscribe(
     (ligne:any[])=>{
       this.ligneFiltre=[]
       for(let i = 0; i < ligne.length; i++){
         var myValue = ligne;
         this.ligneFiltre.push(myValue[i]);
       }
       this.ligneFiltre = this.removeDuplicates(this.ligneFiltre)
       this.ligneFiltre.sort();
     }
   );


   /* Détermine toutes les familles à filtrer */
   this.produitService.recupFamilleFiltre(this.langueSelect);
   this.familleFiltreSubscription=this.produitService.familleFiltreBoxSubject.subscribe(
     (famille:any[])=>{
       console.log(famille);
       this.familleFiltre=[]
       for(let i = 0; i < famille.length; i++){
         var myValue = famille;
         this.familleFiltre.push(myValue[i]);
       }

       this.familleFiltre = this.removeDuplicates(this.familleFiltre)
       this.familleFiltre.sort();
     }
   );


   /* Détermine toutes les sous-familles à filtrer */
   this.produitService.recupSousFamilleFiltre(this.langueSelect);
   this.sousFamilleFiltreSubscription=this.produitService.sousFamilleFiltreBoxSubject.subscribe(
     (sousFamille:any[])=>{
       this.sousFamilleFiltre=[]
       for(let i = 0; i < sousFamille.length; i++){
         var myValue = sousFamille;
         this.sousFamilleFiltre.push(myValue[i]);
       }

       this.sousFamilleFiltre = this.removeDuplicates(this.sousFamilleFiltre)
       this.sousFamilleFiltre.sort();
     }
   );

   /* Détermine toutes les marques à filtrer */
   this.produitService.recupMarqueFiltre(this.langueSelect);
   this.marqueFiltreSubscription=this.produitService.marqueFiltreBoxSubject.subscribe(
     (marque:any[])=>{
       this.marqueFiltre=[]
       for(let i = 0; i < marque.length; i++){
         var myValue = marque;
         this.marqueFiltre.push(myValue[i]);
       }

       this.marqueFiltre = this.removeDuplicates(this.marqueFiltre)
       this.marqueFiltre.sort();
     }
   );


   /* Détermine toutes les themes à filtrer */
   this.produitService.recupThemeFiltre(this.langueSelect);
   this.themeFiltreSubscription=this.produitService.themeFiltreBoxSubject.subscribe(
     (theme:any[])=>{
       this.themeFiltre=[]
       for(let i = 0; i < theme.length; i++){
         var myValue = theme;
         this.themeFiltre.push(myValue[i]);
       }
       this.themeFiltre = this.removeDuplicates(this.themeFiltre)
       this.themeFiltre.sort();
     }
   );

     /* Détermine toutes les modeles à filtrer */
     this.produitService.recupModeleFiltre(this.langueSelect);
     this.modeleFiltreSubscription=this.produitService.modeleFiltreBoxSubject.subscribe(
       (modele:any[])=>{
         this.modeleFiltre=[]
         for(let i = 0; i < modele.length; i++){
           var myValue = modele;
           this.modeleFiltre.push(myValue[i]);
         }
         this.modeleFiltre = this.removeDuplicates(this.modeleFiltre)
         this.modeleFiltre.sort();
       }
     );

  }

  recupFiltres(){
    let myConcatValue = "";
    if(this.ligneSelect===undefined) this.ligneSelect="";
    if(this.familleSelect===undefined) this.familleSelect="";
    if(this.themeSelect===undefined) {
      this.themeSelected = this.themeSelect="";
    }else{
      /* ATTENTION nous sommes obliger de modifier le - par autre chose car sinon il crois que c'est un param url pour separer les filtre dans myConcatValue */
      this.themeSelected = this.themeSelect.replace('-','_');
    };
    if(this.coloriSelect===undefined) this.coloriSelect="";
    if(this.tailleSelect===undefined) this.tailleSelect="";
    if(this.matiereSelect===undefined) this.matiereSelect="";
    if(this.marqueSelect===undefined) this.marqueSelect="";
    if(this.sousFamilleSelect===undefined) this.sousFamilleSelect="";
    if(this.modeleSelect===undefined) this.modeleSelect="";

    myConcatValue = "$-"+this.ligneSelect+"-"+this.familleSelect+"-"+this.themeSelected+"-"+this.coloriSelect+"-"+this.matiereSelect+"-"+this.marqueSelect+"-"+this.sousFamilleSelect+"-"+this.modeleSelect+"-"+this.tailleSelect+"";
    this.filtreService.sendCurrentLigne(this.ligneSelect);
    this.filtreService.sendCurrentFamille(this.familleSelect);
    this.filtreService.sendCurrentTheme(this.themeSelect);
    this.filtreService.sendCurrentColori(this.coloriSelect);
    this.filtreService.sendCurrentTaille(this.tailleSelect);
    this.filtreService.sendCurrentMatiere(this.matiereSelect);
    this.filtreService.sendCurrentMarque(this.marqueSelect);
    this.filtreService.sendCurrentsousFamille(this.sousFamilleSelect);
    this.filtreService.sendCurrentModele(this.modeleSelect);
    this.router.navigate(['/contenu/produits/',myConcatValue]);
  }

  resetFiltres() {
    // this.ligneSelect = "";
    // this.familleSelect = "";
    // this.themeSelect = "";
    // this.coloriSelect = "";
    // this.matiereSelect = "";
    // this.tailleSelect = "";
    // this.sousFamilleSelect = "";
    // this.marqueSelect = "";
    // this.modeleSelect = "";
    this.ligneSelect = undefined;
    this.familleSelect = undefined;
    this.themeSelect = undefined;
    this.coloriSelect = undefined;
    this.matiereSelect = undefined;
    this.tailleSelect = undefined;

    this.chargeFiltres();

  }

  myLangue(){
    var myLangue = this.langueService.getLangue();
    if(myLangue === 'FRA') {
      this.langueSelect = "FRA"
    } else if(myLangue === 'ANG') {
      this.langueSelect = "ANG"
    }
  }

   removeDuplicates(array) {
    let unique = {};
    array.forEach(function(i) {
      if(!unique[i]) {
        unique[i] = true;
      }
    });
    return Object.keys(unique);
  }


  /* recuperer la value de l'input pour renvoyer des donnée correspondant au valeur deja selectionnée dans le filtres */
  getElementAndReturnNewArrayInFilter(){
    /* ------------FILTRE LIGNE (genre) ------------------*/
    if(this.ligneSelect != undefined){
     this.familleFiltre = [];
     this.coloriFiltre = [];
     for(let produit of this.produitService.produits){
       if(produit['ligne'] == this.ligneSelect){
         if(!this.familleFiltre.includes(produit['famille'])){
           this.familleFiltre.push(produit['famille'])
           this.familleFiltre.sort()
         }
         if(!this.coloriFiltre.includes(produit['libcolori'])){
           this.coloriFiltre.push(produit['libcolori'])
           this.coloriFiltre.sort()
         }
       }
      }
     }

    /* ------------FILTRE FAMILLE (categorie) ------------------*/
      if(this.familleSelect != undefined){
       this.coloriFiltre = [];
        for(let produit of this.produitService.produits){
          if(produit['famille'] === this.familleSelect){
            if(!this.coloriFiltre.includes(produit['libcolori'])){
              this.coloriFiltre.push(produit['libcolori'])
              this.coloriFiltre.sort()
             }
           }
        }
       }

        console.log('this.coloriSelect '+ this.coloriSelect);
        console.log('this.familleSelect '+ this.familleSelect);
        console.log('this.tailleSelect '+ this.tailleSelect);
        console.log('this.ligneSelect '+ this.ligneSelect);

    }
  }
