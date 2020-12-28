import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router'
import { BreakpointObserver } from '@angular/cdk/layout';
import { ProduitService } from '../../services/produits.service';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../../services/http-request.service';
import { MatSnackBar } from '@angular/material';
import { Subscription } from 'rxjs';
import { TemplateService } from '../../services/template.service';
import { TranslateService,LangChangeEvent } from '@ngx-translate/core';

@Component({
  selector: 'app-history-representant',
  templateUrl: './history-representant.component.html',
  styleUrls: ['./history-representant.component.css']
})
export class HistoryRepresentantComponent implements OnInit {
  isDesktop:boolean;
  isMobile:boolean;
  isTablet:boolean;
  tailleEcran:number=1;
  loginRepresentant:string="";
  listeCommandes:any[]=[];  // Regroupe la liste des commandes
  currentDetails:any[]=[];  // Regroupe le détail de chaque commande
  selected:boolean[]=[];  // Permet de voir si la ligne est séléctionné
  previousIndex:number=-1;
  contenuColor:string;
  contenuColorSubscription:Subscription;
  commandesExiste:boolean;
  pbAffichage:string;

  constructor(private router:Router, private breakPoint:BreakpointObserver, private produitService:ProduitService, private httpRequest:HttpRequest, private httpClient:HttpClient, private snackBar:MatSnackBar, private templateService:TemplateService,translate: TranslateService) { 

    
    translate.get('representant.pbAffichage').subscribe((res: string) => {
      this.pbAffichage = res;
    });
    
    translate.onLangChange.subscribe((event: LangChangeEvent) => {
      translate.get('representant.pbAffichage').subscribe((res: string) => {
        this.pbAffichage = res;
      });
    });

    breakPoint.observe([
      '(max-width: 1288px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.tailleEcran=2; //si atteint 1288px passe la taille à 2 --> 3 produits
      }else{
        this.tailleEcran=1; //sinon revient à une taille à 1 --> 4 produits
        var modules = document.getElementById("modules");
        modules.style.position="relative";
        modules.style.marginRight="2.5%";
        modules.style.marginLeft="0%";
        modules.style.marginTop="0px";
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
        if(Number(window.innerWidth)<785){
          this.tailleEcran=3 //sinon si la taille est inférieure à 785px revient à une taille à 3 --> 2 produits
        }
      }
    });
    breakPoint.observe([
      '(max-width: 1000px)'
    ]).subscribe(result => {
      if(result.matches){
        this.appModdules(); //lorsqu'on atteint une taille < à 1000px affiche les modules en bas du contenu
      }
    })
  }

  ngOnInit() {
    if(sessionStorage.getItem("representant")!=='true'){
      this.router.navigate(['']);
      sessionStorage.clear();
    }

    this.templateService.getContenuColor();
    this.contenuColorSubscription=this.templateService.contenuColorSubject.subscribe(
      (contenuColor:string)=>{
        this.contenuColor='#'+contenuColor;
      }
    );
    this.templateService.emitContenuColor();

    this.isDesktop=this.produitService.isDesktop; //renvoi true si Desktop
    this.isMobile=this.produitService.isMobile; //renvoi true si Mobile
    this.isTablet=this.produitService.isTablet; //renvoi true si tablet

    this.loginRepresentant=sessionStorage.getItem("loginRepresentant");
    this.httpClient.post(this.httpRequest.InfoApprouveur,{
      "loginRepresentant":this.loginRepresentant
    }).subscribe(data=>{
      this.commandesExiste = data[0];
      if(data[0] === true){
        this.listeCommandes = data[1];
        for(let uneCommande of this.listeCommandes) {
          this.selected.push(false);
        }
      }
    });
  }

  afficherDetails(numCommande: number, index: number) {
      // Si on clique sur un nouvelle index, on affiche les détails de la commande
    if(index !== this.previousIndex) {
      this.selected[this.previousIndex] = false;
      this.previousIndex = index;
      this.selected[index] = true;
      this.httpClient.post(this.httpRequest.InfoDetailsCommande,{
        "numCommande":numCommande
      }).subscribe(data=>{
        if(data[0] === true) {
          this.currentDetails = data[1];
        } else {
          this.snackBar.open(this.pbAffichage,"",{
            duration: 2500
          });
        }
      });
      // Sinon on ferme les détails affichés
    } else {
      this.previousIndex = -1;
      this.selected[index] = false;
      this.currentDetails = [];
    }
  }

  appModdules(){ //permet d'afficher les modules en bas du contenu lorsque la largeur de la page est inférieure à 1000px
    var modules = document.getElementById("modules");
    modules.style.height="auto";
    modules.style.marginLeft="2.5%";
    modules.style.marginRight="2.5%";
    modules.style.position="relative";
    modules.style.marginTop="5%";
  }

  }
