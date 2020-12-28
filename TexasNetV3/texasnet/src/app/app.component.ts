import { Component, OnInit, AfterViewInit, ElementRef, OnDestroy } from '@angular/core';
import { AuthService } from '../app/services/auth.service';
import { ModuleService } from './services/modules.service';
import { DeviceDetectorService } from 'ngx-device-detector';
import { FiltreService } from './services/filtre.service';
import { Subscription } from "rxjs";
import { CommandeService } from "./services/commandes.service";
import { TemplateService } from './services/template.service';
import { TranslateService } from '@ngx-translate/core';
import { MenuService } from './services/menu.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit,AfterViewInit, OnDestroy {
  isDesktop:boolean;
  isMobile:boolean;
  isTablet:boolean;
  constructor(private templateService:TemplateService,private elementRef:ElementRef,private commandeService:CommandeService,public authService:AuthService, private moduleService:ModuleService, private deviceDetector:DeviceDetectorService, private filtreService:FiltreService,private translate: TranslateService, private menuService:MenuService) {
    translate.setDefaultLang('fr');
}
  authStatut:boolean;
  title = 'texasnet';
  filtreSelection:boolean=true;
  filtreSelectionSubscription:Subscription;
  displayModalPanier:boolean=false;
  displayModalPanierSubscription:Subscription;
  displayConfirmBox:boolean=false;
  displayConfirmBoxSubscription:Subscription;
  isAdmin:boolean;
  backgroundColor:string;
  backgroundColorSubscription:Subscription;

  ngOnInit(){
    this.authStatut=this.authService.isAuth;
    this.isDesktop=this.deviceDetector.isDesktop();
    this.isMobile=this.deviceDetector.isMobile();
    this.isTablet=this.deviceDetector.isTablet();
    this.filtreSelectionSubscription=this.filtreService.filtreSelectionSubject.subscribe(
      (filtreSelection:boolean)=>{
        this.filtreSelection=filtreSelection
      }
    );
    this.filtreService.emitFiltreSelection();
    this.displayModalPanierSubscription=this.commandeService.displayModalPanierSubject.subscribe(
      (display:boolean)=>{
        this.displayModalPanier=display;
      }
    );
    this.commandeService.emitDisplayModal();
    this.displayConfirmBoxSubscription=this.commandeService.displayConfirmBoxSubject.subscribe(
      (display:boolean)=>{
        this.displayConfirmBox=display;
      }
    );
    this.commandeService.emitDisplayConfirm();
  }

  /* Modifie lle background-color du body */
  ngAfterViewInit(){
    this.backgroundColorSubscription=this.templateService.backgroundColorSubject.subscribe(
      (backgroundColor:string)=>{
        this.elementRef.nativeElement.ownerDocument.body.style.backgroundColor='#'+backgroundColor;
      }
    );
    this.templateService.emitBackGroundColor();
  }

  ngOnDestroy(){
    this.backgroundColorSubscription.unsubscribe();
  }

}
