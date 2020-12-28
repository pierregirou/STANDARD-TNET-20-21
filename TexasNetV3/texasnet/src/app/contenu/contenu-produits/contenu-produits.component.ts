import { Component, OnInit, Input } from '@angular/core';
import { BreakpointObserver } from '@angular/cdk/layout';
import { ProduitService } from '../../services/produits.service';
import { CommandeService } from '../../services/commandes.service';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../../services/http-request.service';
import { ModuleService } from '../../services/modules.service';
import { TemplateService } from '../../services/template.service';
import { Subscription } from 'rxjs';
import { SelectionMenuService } from '../../services/selection-menu.service';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';
@Component({
  selector: 'app-contenu-produits',
  templateUrl: './contenu-produits.component.html',
  styleUrls: ['./contenu-produits.component.css']
})

export class ContenuProduitsComponent implements OnInit {
  isDesktop:boolean;
  isMobile:boolean;
  isTablet:boolean;
  visGalerie:boolean;
  ordreAffichage:string;
  tailleEcran:number=1; //Variable permettant de connaitre le nombre de produits à afficher
  contenuColorSubscription:Subscription;
  contenuColor:string;
  recherche:string;
  @Input() artInfo:string;

  constructor(private templateService:TemplateService,private produitService:ProduitService,private breakPoint:BreakpointObserver, private commandeService:CommandeService, private httpClient:HttpClient, private httpRequest:HttpRequest, private moduleServices:ModuleService,private route:ActivatedRoute,private router:Router,private selectionmenuService:SelectionMenuService) {
    this.commandeService.initCommande(); //initialisation d'une commande
    //Utilisation de breakpoint pour détecter un changement de la taille de l'écran en largeur
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
    this.moduleServices.displayOrder().then(data=>{
      this.ordreAffichage=String(data['ordreAffichage']);
    });
  }

  searchInput(value){ //détecte la saisie dans la barre de recherche et envoie au service la valeur entrée en input
    this.recherche = value.toUpperCase();
  }

  //fonction permettant de revenir en haut de la page
  hautPage(){
    window.top.window.scrollTo(0,0); //renvoi en haut
    document.getElementById('cRetour').className="cInvisible"; //une fois en haut cache le bouton
  }

  appModdules(){ //permet d'afficher les modules en bas du contenu lorsque la largeur de la page est inférieure à 1000px
    var modules = document.getElementById("modules");
    modules.style.height="auto";
    modules.style.marginLeft="2.5%";
    modules.style.marginRight="2.5%";
    modules.style.position="relative";
    modules.style.marginTop="5%";
  }

  getDisplayOrder(id:number){
    this.httpClient.post(this.httpRequest.UpdateModules,{
      "ordreAffichage":id
    }).subscribe(data=>{
      this.produitService.recupProduit("FRA");
      this.produitService.emitProduits();
    })
  }
  
}
