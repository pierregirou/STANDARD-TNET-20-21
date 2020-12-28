import { Component, OnInit, OnDestroy } from '@angular/core';
import { FiltreService } from '../services/filtre.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-filtre-mobile',
  templateUrl: './filtre-mobile.component.html',
  styleUrls: ['./filtre-mobile.component.css']
})
export class FiltreMobileComponent implements OnInit, OnDestroy {

  actionCouleur:boolean=false;
  actionTaille:boolean=false;
  actionTheme:boolean=false;
  actionPrix:boolean=false;
  /* Les filtres disponibles depuis le service filtre */
  colorTab:any[]=[];
  tailleTab:any[]=[];
  colorTabSubscription:Subscription;
  tailleTabSubscription:Subscription;
  /* *** */
  /* Résultat des requêtes filtres (couleur,taille...) */
  selectCouleur:any[]=[];
  selectTaille:any[]=[];
  /* Connaître le nombre d'option coché par l'utilisateur */
  countColor:number=0;
  countTaille:number=0;
    /* Switch tout sélectionner -->effacer pour couleur */
  messageCouleur:string="Tout sélectionner"
    /* Switch tout sélectionner -->effacer pour taille */
  messageTaille:string="Tout sélectionner"
  constructor(private filtreService:FiltreService) { }

  ngOnInit() {
    this.colorTabSubscription=this.filtreService.colorTabSubject.subscribe(
      (color:any[])=>{
        this.colorTab=color;
      }
    );
    this.filtreService.emitColorTab();

    this.tailleTabSubscription=this.filtreService.tailleTabSubject.subscribe(
      (taille:any[])=>{
        this.tailleTab=taille;
      }
    );
    this.filtreService.emitTailleTab();
  }

  onSelect(type:string,choix:string){
    switch(type){
      case 'couleur':
        if(choix==='false'){
          this.actionCouleur=false;
        }
        if(choix==='true'){
          this.actionCouleur=true;
        }
        break;
      case 'taille':
        if(choix==='false'){
          this.actionTaille=false;
        }
        if(choix==='true'){
          this.actionTaille=true;
        }
        break;
      case 'theme':
        if(choix=='false'){
          this.actionTheme=false;
        }
        if(choix=='true'){
          this.actionTheme=true;
        }
    }
  }

  //méthode pour récupérer les couleurs sélectionnées par l'utilisateur 
  getColor(index:number,select:number){
    if(select===0){ //si la couleur est à select===0 la passe à 1
      this.colorTab[index].select=1;
      this.countColor++;
      if(this.countColor>0){
        this.messageCouleur="Effacer";
      }
    }
    if(select===1){ //si la couleur est à select===1 la passe à 0
      this.colorTab[index].select=0;
      this.countColor--;
      if(this.countColor===0){
        this.messageCouleur="Tout sélectionner"
      }
    }
  }

  //méthode pour récupérer les tailles sélectionnées par l'utilisateur
  getTaille(index:number,select:number){
    if(select===0){
      this.tailleTab[index].select=1; //si la taille est à select===0 la passe à 1
      this.countTaille++;
      if(this.countTaille>0){
        this.messageTaille="Effacer";
      }
    }
    if(select===1){
      this.tailleTab[index].select=0; //si la taille est à select===1 la passe à 0
      this.countTaille--;
      if(this.countTaille===0){
        this.messageTaille="Tout sélectionner";
      }
    }
  }

  selectAll(type:string){ /* Permet de sélectionner toutes les options présentes dans les filtres (couleur,taille...) */
    /* Pour les couleurs */
    if(type==="couleur" && this.countColor===0 && this.messageCouleur==="Tout sélectionner"){
      for(let i=0;i<Object.keys(this.colorTab).length;i++){
        this.colorTab[i].select=1;
      }
      this.countColor=Object.keys(this.colorTab).length;
      this.messageCouleur="Effacer";
    }else if(type==="couleur" && this.countColor>0 && this.messageCouleur==="Effacer"){
      for(let i=0;i<Object.keys(this.colorTab).length;i++){
        this.colorTab[i].select=0;
        this.countColor=0;
      }
      this.messageCouleur="Tout sélectionner";
    }
    /* *** */
    /* Pour les tailles */
    if(type==="taille" && this.countTaille===0 && this.messageTaille==="Tout sélectionner"){
      for(let i=0;i<Object.keys(this.tailleTab).length;i++){
        this.tailleTab[i].select=1;
      }
      this.countTaille=Object.keys(this.tailleTab).length;
      this.messageTaille="Effacer";
    }else if(type==="taille" && this.countTaille>0 && this.messageTaille==="Effacer"){
      for(let i=0;i<Object.keys(this.tailleTab).length;i++){
        this.tailleTab[i].select=0;
        this.countTaille=0;
      }
      this.messageTaille="Tout sélectionner";
    }
  }

  apply(type:string){
    if(type==='couleur'){
      this.selectCouleur=[];
      for(let i=0;i<Object.keys(this.colorTab).length;i++){
        if(this.colorTab[i].select===1){
          this.selectCouleur.push(this.colorTab[i].color);
        }
      }
    }
    if(type==='taille'){
      this.selectTaille=[];
      for(let i=0;i<Object.keys(this.tailleTab).length;i++){
        if(this.tailleTab[i].select===1){
          this.selectTaille.push(this.tailleTab[i].taille);
        }
      }
    }
  }

  returnProducts(){
    this.filtreService.filtreSelection=false;
    this.filtreService.emitFiltreSelection();
  }


  ngOnDestroy(){
    this.colorTabSubscription.unsubscribe();
    this.tailleTabSubscription.unsubscribe();
  }

}
