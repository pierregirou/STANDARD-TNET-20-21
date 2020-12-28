import { Component, OnInit, Output, OnDestroy, EventEmitter } from '@angular/core';
import { BreakpointObserver } from '@angular/cdk/layout';
import { FiltreService } from '../services/filtre.service';
import { ProduitService } from "../services/produits.service";
import { Subscription } from "rxjs";
import { Colori } from '../models/coloris.models';
import { ModuleService } from "../services/modules.service";
import { TailleFiltre } from '../models/tailleFiltre.models';
import { SelectionMenuService } from "../services/selection-menu.service";
import { ActivatedRoute } from '@angular/router';
import { isDefined } from '@angular/compiler/src/util';

@Component({
  selector: 'app-filtre',
  templateUrl: './filtre.component.html',
  styleUrls: ['./filtre.component.css']
})
export class FiltreComponent implements OnInit, OnDestroy {

  @Output() changementFiltre = new EventEmitter;
  tailleEcran:number=1;
  displayReset:boolean=false; //si un filtre est appliqué le bouton reset est affiché par défaut non
  mode:string;
  coloriFiltre:Colori[];
  coloriFiltreSubscription:Subscription;
  tailleFiltre:TailleFiltre[];
  tailleFiltreSubscription:Subscription;
  gammeTaille:any[];
  gammeTailleSubscription:Subscription;
    // Params
  precedenteSelection:string = '';
  choixColori:string[]=[];
  choixTaille:any[]=[];
  displayFilter:any[] = [];  // Affichage modal


  constructor(private moduleService:ModuleService,private produitService:ProduitService, private breakPoint:BreakpointObserver, private filtreService:FiltreService, private selectionMenuService:SelectionMenuService, private route:ActivatedRoute) {
    //Utilisation de breakpoint pour détecter un changement de la taille de l'écran en largeur
    breakPoint.observe([
      '(max-width: 1288px)'
    ]).subscribe(result => {
      if (result.matches) {
        //document.getElementById("contenuFiltreItem").style.width="100%"; //élargit la zone d'affichage des boutons de trie
        this.tailleEcran=2;
      }else{
        //document.getElementById("contenuFiltreItem").style.width="60%"; //remet la zone d'affichage des boutons de trie à 60%
        this.tailleEcran=1;
      }
    });
    this.displayFilter['color'] = false;
    this.displayFilter['size'] = false;
  }

  ngOnInit() {

    /* determine si mode tableau -->1 ou mode ligne -->2 */
    this.moduleService.modeSaisie().then(
      (mode)=>{
        if(mode==="1"){
          this.mode="tableau";
        }
        if(mode==="2"){
          this.mode="ligne";
        }
      }
    )
    /* Détermine tous les coloris à filtrer */
    this.coloriFiltreSubscription=this.selectionMenuService.coloriFiltreSubject2.subscribe(
      (colori:Colori[])=>{
        this.coloriFiltre=colori;
      }
    );

    /* Détermine toutes les tailles à filtrer */
    this.produitService.recupTailleFiltre("FRA");
    this.tailleFiltreSubscription=this.produitService.tailleFiltreSubject.subscribe(
      (taille:TailleFiltre[])=>{
        this.tailleFiltre=taille
      }
    );

    /*
    this.gammeTailleSubscription=this.selectionMenuService.codeGammeSubject.subscribe(
      (gammes)=>{
      this.codeGamme = gammes;
    });*/

    this.produitService.emitTailleFiltre();
    this.route.params.subscribe(params=>{
          // Si on change de selection c'est comme si on avait changé de page donc on reset le filtre
        if (this.precedenteSelection !== params["selection"]) {
          this.numberFilterColor = 0;
          this.displayReset = false;
          if (typeof(this.coloriFiltre) !== 'undefined') {
            for(let uneCouleur of this.coloriFiltre) {
              uneCouleur.select = 0;
            }
          }
          for (let type in this.displayFilter) {
            this.displayFilter[type] = false;
          }
        }
        typeof(params["selection"]) !== 'undefined' ? this.precedenteSelection = params["selection"] : this.precedenteSelection = '';
      });
  }
   numberFilterColor:number=0; //Pour les couleurs permet d'indiquer le nombre de couleurs cochées
   numberFilterTaille:number=0 //Pour les tailles permet d'indique le nombre de couleurs cochées

    // Modal display handler
   clickFilter(paramType:string) {
     for (let type in this.displayFilter) {
       type == paramType ? this.displayFilter[type] = !this.displayFilter[type] : this.displayFilter[type] = false;
     }
   }

    // Stockage des paramètres lors de la séléction
  selectParam(typeParam:string, index:number, codeGammeTaille:number = 0) {
    switch (typeParam) {
      case 'color':
        if (this.coloriFiltre[index].select === 0) {
          this.coloriFiltre[index].select = 1;
          this.choixColori.push(this.coloriFiltre[index].libelle);
        } else {
          this.coloriFiltre[index].select = 0;
          const tmp = this.choixColori.indexOf(this.coloriFiltre[index].libelle);
          if (tmp > -1) {
            this.choixColori.splice(tmp,1);
          }
        }
      break;
      case 'size':
        const currentSize = this.tailleFiltre[codeGammeTaille].tailles[index][0];
        const currentGamme = this.tailleFiltre[codeGammeTaille].codeGammeTaille
        if (currentSize.select === 0) {
          this.tailleFiltre[codeGammeTaille].tailles[index][0].select = 1;
          this.choixTaille.push([currentGamme,currentSize.taille]);
        } else {
          this.tailleFiltre[codeGammeTaille].tailles[index][0].select = 0;
          const tmp = this.choixTaille.findIndex(item => item[0] === currentGamme && item[1] === currentSize.taille);
          if (tmp > -1) {
            this.choixTaille.splice(tmp,1);
          }
        }
      break;
    }
  }

  appliquerFiltre() {
    this.choixColori.length > 0 || this.choixTaille.length > 0 ? this.displayReset = true : this.displayReset = false;
    for (let type in this.displayFilter) {
      this.displayFilter[type] = false;
    }
    this.produitService.filtreApply2([this.precedenteSelection,this.choixColori,this.choixTaille,[]]);
    this.changementFiltre.emit(true);
  }

   ngOnDestroy(){
   }

   resetFilter(){
    for(let uneCouleur of this.coloriFiltre) {
      uneCouleur.select = 0;
    }
    for(let uneTaille of this.tailleFiltre) {
      for(let uneTaille2 of uneTaille.tailles) {
        uneTaille2[0].select = 0;
      }
    }
    for (let type in this.displayFilter) {
      this.displayFilter[type] = false;
    }
    this.choixColori = [];
    this.choixTaille = [];
    this.produitService.filtreApply2([this.precedenteSelection,this.choixColori,this.choixTaille,[]]);
    this.changementFiltre.emit(false);
    this.displayReset=false;
    this.ngOnInit();
   }
}
