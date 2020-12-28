import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';
import { Subscription } from "rxjs";

@Injectable()
export class FiltreService{
    colorTab=[
        {
          color:"red",
          select:0
        },
        {
          color:"blue",
          select:0
        }
      ];

      /* Tableau list des tailles test */
      tailleTab=[
        {
          taille:"38",
          select:0
        },
        {
          taille:"39",
          select:0
        }
      ];

      colorTabSubject=new Subject<any[]>();
      tailleTabSubject=new Subject<any[]>();

      ligneSubject=new Subject<any>();
      familleSubject=new Subject<any>();
      themeSubject=new Subject<any>();
      coloriSubject=new Subject<any>();
      tailleSubject=new Subject<any>();
      matiereSubject=new Subject<any>();
      marqueSubject = new Subject<any>();
      sousFamilleSubject = new Subject<any>();
      modeleSubject = new Subject<any>();


      currentLigne:string;
      currentFamille:string;
      currentTheme:string;
      currentColori:string;
      currentTaille:string;
      currentMatiere:string;
      currentMarque:string;
      currentSousFamille: string;
      currentModele: string;

      emitColorTab(){
          this.colorTabSubject.next(this.colorTab.slice());
      }

      emitTailleTab(){
          this.tailleTabSubject.next(this.tailleTab.slice());
      }

      filtreSelection:boolean=false;
      filtreSelectionSubject=new Subject<boolean>()

      emitFiltreSelection(){
        this.filtreSelectionSubject.next(this.filtreSelection);
      }

      sendCurrentLigne(ligne:string) {
        this.currentLigne = ligne;
        this.ligneSubject.next(this.currentLigne);
      }
      sendCurrentFamille(famille:string) {
        this.currentFamille = famille;
        this.familleSubject.next(this.currentFamille);
      }
      sendCurrentTheme(theme:string) {
        this.currentTheme = theme;
        this.themeSubject.next(this.currentTheme);
      }
      sendCurrentColori(colori:string) {
        this.currentColori = colori;
        this.coloriSubject.next(this.currentColori);
      }
      sendCurrentTaille(taille:string) {
        this.currentTaille = taille;
        this.tailleSubject.next(this.currentTaille);
      }
      sendCurrentMatiere(matiere:string) {
        this.currentMatiere = matiere;
        this.matiereSubject.next(this.currentMatiere);
      }
      sendCurrentMarque(marque:string) {
        this.currentMarque = marque;
        this.marqueSubject.next(this.currentMarque);
      }
      sendCurrentsousFamille(sousFamille:string) {
        this.currentSousFamille = sousFamille;
        this.sousFamilleSubject.next(this.currentSousFamille);
      }
      sendCurrentModele(modele:string) {
        this.currentModele = modele;
        this.modeleSubject.next(this.currentModele);
      }

      getCurrentLine() {
        return this.currentLigne;
      }
      getCurrentFamille() {
        return this.currentFamille;
      }
      getCurrentTheme() {
        return this.currentTheme;
      }
      getCurrentColori() {
        return this.currentColori;
      }
      getCurrentTaille() {
        return this.currentTaille;
      }
      getCurrentMatiere() {
        return this.currentMatiere;
      }
      getCurrentMarque() {
        return this.currentMarque;
      }
      getCurrentsousFamille() {
        return this.currentSousFamille;
      }
      getCurrentModele() {
        return this.currentModele;
      }
      resetFiltres() {
        this.currentLigne = "";
        this.currentFamille = "";
        this.currentTheme = "";
        this.currentColori = "";
        this.currentTaille = "";
        this.currentMatiere = "";
        this.currentMarque = "";
        this.currentSousFamille = "";
        this.currentModele = "";

      }
}
