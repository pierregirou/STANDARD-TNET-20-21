import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { AppComponent } from './app.component';
import { ConnexionComponent } from './connexion/connexion.component';
import { BaniereComponent } from './baniere/baniere.component';
import { FooterComponent } from './footer/footer.component';
import { MenuComponent } from './menu/menu.component';
import { ContenuAccueilComponent } from './contenu/contenu-accueil/contenu-accueil.component'
import { ContenuPromoComponent } from './contenu/contenu-promo/contenu-promo.component';
import { ContenuTelechargementsComponent } from './contenu/contenu-telechargements/contenu-telechargements.component';
import { ContenuPanierComponent } from './contenu/contenu-panier/contenu-panier.component';
import { ContenuPointsComponent } from './contenu/contenu-points/contenu-points.component';
import { ContenuCompteComponent } from './contenu/contenu-compte/contenu-compte.component';
import { InfoComponent } from './info/info.component';
import { TotalComponent } from './total/total.component';
import { Routes, RouterModule } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { AuthService } from '../app/services/auth.service';
import { AuthGuard } from '../app/services/auth-guard.service';
//Ajout des icons de fontawesome
import { FontAwesomeModule } from '@fortawesome/angular-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { ContenuComponent } from './contenu/contenu.component';
import { FourOhFourComponent } from './four-oh-four/four-oh-four.component';
import { InformationService } from '../app/services/informations.service';
import { ProduitService } from '../app/services/produits.service';
/*import { MatStepperModule } from '@angular/material/stepper';
import {MatFormFieldModule} from '@angular/material/form-field';
import { MatInputModule, MatIconModule, MatButtonModule , MatAutocompleteModule } from '@angular/material';*/
import { ModuleService } from './services/modules.service';
import { CommandeService } from './services/commandes.service';
import { DeviceDetectorModule } from 'ngx-device-detector';
import { HttpRequest } from './services/http-request.service';
import { ImageService } from "./services/images.service";
import { FiltreService } from './services/filtre.service';
import { DragDropModule } from '@angular/cdk/drag-drop';
import { NgxAutoScrollModule } from 'ngx-auto-scroll';
import { safeHtmlPipe } from './pipe/safeHtml.pipe';
import { productFilter } from './pipe/safeHtml.pipe';
//import { searchFilterPipe } from './pipe/safeHtml.pipe';
import { NgxImageZoomModule } from 'ngx-image-zoom';
import { ScrollToModule } from '@nicky-lenaers/ngx-scroll-to';
import {TranslateLoader, TranslateModule} from '@ngx-translate/core';
import {TranslateHttpLoader} from '@ngx-translate/http-loader';
import {
  MatAutocompleteModule,
  MatButtonModule,
  MatButtonToggleModule,
  MatCardModule,
  MatCheckboxModule,
  MatChipsModule,
  MatDatepickerModule,
  MatDialogModule,
  MatDividerModule,
  MatExpansionModule,
  MatGridListModule,
  MatIconModule,
  MatInputModule,
  MatListModule,
  MatMenuModule,
  MatNativeDateModule,
  MatPaginatorModule,
  MatProgressBarModule,
  MatProgressSpinnerModule,
  MatRadioModule,
  MatRippleModule,
  MatSelectModule,
  MatSidenavModule,
  MatSliderModule,
  MatSlideToggleModule,
  MatSnackBarModule,
  MatSortModule,
  MatStepperModule,
  MatTableModule,
  MatTabsModule,
  MatToolbarModule,
  MatTooltipModule,
  MatFormFieldModule,
  MatBadgeModule,
  MAT_DATE_FORMATS,
  MAT_DATE_LOCALE
} from '@angular/material';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { VisuSiteComponent } from './visu-site/visu-site.component';
import { DetailService } from './services/detail-produit.service';
import { InfiniteScrollModule } from 'ngx-infinite-scroll';
import { ContenuProduitsComponent } from './contenu/contenu-produits/contenu-produits.component';
import { ContenuProduitsDesktopComponent } from './contenu/contenu-produits/contenu-produits-desktop/contenu-produits-desktop.component';
import { FiltreComponent } from './filtre/filtre.component';
import { DetailProduitComponent } from './detail-produit/detail-produit.component';
import { FiltreMobileComponent } from './filtre-mobile/filtre-mobile.component';
import { ModalPanierComponent } from './modal-panier/modal-panier.component';
import { ConfirmBoxComponent } from './confirm-box/confirm-box.component';
import { CommandeTailleComponent } from './commande-taille/commande-taille.component';
import { GestionComponent } from './gestion/gestion.component';
import { TelechargementService } from './services/telechargement.service';
import { ContenuMaintenanceComponent } from './contenu/contenu-maintenance/contenu-maintenance.component';
import { ContenuLooksComponent } from './contenu/contenu-looks/contenu-looks.component';
import { AdministrationComponent } from './administration/administration.component';
import { RepresentantComponent } from './representant/representant.component';
import { MenuAdministrationComponent } from './administration/menu-administration/menu-administration.component';
import { ContenuAdministrationComponent } from './administration/contenu-administration/contenu-administration.component';
import { ParametresAdministrationComponent } from './administration/contenu-administration/parametres-administration/parametres-administration.component';
import { GestionProduitsComponent } from './administration/contenu-administration/gestion-produits/gestion-produits.component';
import { AdministrationService } from './services/administration.service';
import { MenuRepresentantComponent } from './representant/menu-representant/menu-representant.component';
import { CKEditorModule } from 'ngx-ckeditor';
import { LangueService } from './services/langue.service';
import { TemplateService } from './services/template.service';
import { ContenuRepresentantComponent } from './representant/contenu-representant/contenu-representant.component';
import { ContenuProduitsSelectionComponent } from './contenu/contenu-produits-selection/contenu-produits-selection.component';
import { SelectionMenuService } from './services/selection-menu.service';
import { ContenuPromoSelectionComponent } from './contenu/contenu-promo-selection/contenu-promo-selection.component';
import { TelechargementAdministrationComponent } from './administration/telechargement-administration/telechargement-administration.component';
import { ApprouveurComponent } from './approuveur/approuveur.component';
import { HistoryRepresentantComponent } from './representant/history-representant/history-representant.component';
import { GestionFdpComponent } from './administration/contenu-administration/gestion-fdp/gestion-fdp.component';
import { FiltreBoxComponent } from './filtre-box/filtre-box.component';
import { InscriptionComponent } from './connexion/inscription/inscription.component';

library.add(fas);

//Création de nouvelles routes
const appRoute: Routes = [
  { path:'connexion', component:ConnexionComponent },
  { path:'connexion/inscription', component:InscriptionComponent },
  { path:'contenu/:menu', canActivate:[AuthGuard], component:ContenuComponent },
  { path:'contenu/compte/:action', canActivate:[AuthGuard],  component:ContenuComponent },
  { path:'accueil', component:VisuSiteComponent },
  { path:'texasnet/:type',component:VisuSiteComponent },
  { path:'texasnet/accueil',component:VisuSiteComponent },
  { path:'not-found',component:FourOhFourComponent },
  { path:'contenu/produits/:selection', component:ContenuProduitsSelectionComponent },
  { path:'contenu/promo/:selection2', component:ContenuPromoSelectionComponent },
  { path:'detail-produit/:refproduit',component:DetailProduitComponent },
  { path:'detail-produit/:refproduit/:prix',component:DetailProduitComponent },
  { path: 'filtre', component:FiltreMobileComponent },
  { path: 'gestion',component:GestionComponent },
  { path: 'administration', component:AdministrationComponent },
  { path: 'administration/:action', component:AdministrationComponent },
  { path: 'representant', component:RepresentantComponent },
  { path: 'approuveur/:type', component:ApprouveurComponent},
  /*{ path:'' , component:VisuSiteComponent },*/
  { path:'', redirectTo:'/texasnet/accueil', pathMatch: 'full' },
  { path:'**',redirectTo:'not-found' }

]

@NgModule({
  exports: [
    MatAutocompleteModule,
    MatButtonModule,
    MatButtonToggleModule,
    MatCardModule,
    MatCheckboxModule,
    MatChipsModule,
    MatStepperModule,
    MatDatepickerModule,
    MatDialogModule,
    MatDividerModule,
    MatExpansionModule,
    MatGridListModule,
    MatIconModule,
    MatInputModule,
    MatListModule,
    MatMenuModule,
    MatNativeDateModule,
    MatPaginatorModule,
    MatProgressBarModule,
    MatProgressSpinnerModule,
    MatRadioModule,
    MatRippleModule,
    MatSelectModule,
    MatSidenavModule,
    MatSliderModule,
    MatSlideToggleModule,
    MatSnackBarModule,
    MatSortModule,
    MatTableModule,
    MatTabsModule,
    MatToolbarModule,
    MatTooltipModule,
    MatBadgeModule
  ],
  declarations: [
    safeHtmlPipe,
    productFilter,
    //searchFilterPipe,
    AppComponent,
    ConnexionComponent,
    BaniereComponent,
    FooterComponent,
    MenuComponent,
    ContenuAccueilComponent,
    ContenuPromoComponent,
    ContenuTelechargementsComponent,
    ContenuPanierComponent,
    ContenuPointsComponent,
    ContenuCompteComponent,
    InfoComponent,
    TotalComponent,
    ContenuComponent,
    FourOhFourComponent,
    VisuSiteComponent,
    ContenuProduitsComponent,
    ContenuProduitsDesktopComponent,
    FiltreComponent,
    DetailProduitComponent,
    FiltreMobileComponent,
    ModalPanierComponent,
    ConfirmBoxComponent,
    CommandeTailleComponent,
    GestionComponent,
    ContenuMaintenanceComponent,
    AdministrationComponent,
    RepresentantComponent,
    MenuAdministrationComponent,
    ContenuAdministrationComponent,
    ParametresAdministrationComponent,
    GestionProduitsComponent,
    ContenuLooksComponent,
    AdministrationComponent,
    MenuRepresentantComponent,
    ContenuRepresentantComponent,
    ContenuProduitsSelectionComponent,
    ContenuPromoSelectionComponent,
    TelechargementAdministrationComponent,
    ApprouveurComponent,
    HistoryRepresentantComponent,
    GestionFdpComponent,
    FiltreBoxComponent,
    InscriptionComponent
  ],
  imports: [
    BrowserModule,
    NgxImageZoomModule.forRoot(),
    FontAwesomeModule,
    RouterModule.forRoot(appRoute),
    FormsModule,
    HttpClientModule,
    TranslateModule.forRoot({
        loader: {
            provide: TranslateLoader,
            useFactory: HttpLoaderFactory,
            deps: [HttpClient]
        }
    }),
    ReactiveFormsModule,
    MatStepperModule,
    MatFormFieldModule,
    MatInputModule,
    BrowserAnimationsModule,
    MatIconModule,
    MatButtonModule,
    MatAutocompleteModule ,
    MatTabsModule,
    MatSelectModule,
    MatRippleModule,
    MatMenuModule,
    MatTableModule,
    MatTooltipModule,
    MatListModule,
    InfiniteScrollModule,
    DeviceDetectorModule.forRoot(),
    MatPaginatorModule,
    MatCheckboxModule,
    MatDialogModule,
    MatRadioModule,
    MatDatepickerModule,
    DragDropModule,
    NgxAutoScrollModule,
    MatCardModule,
    MatSlideToggleModule,
    MatBadgeModule,
    CKEditorModule,
    ScrollToModule.forRoot()
  ],
  providers: [
    AuthService,
    AuthGuard,
    InformationService,
    ProduitService,
    ModuleService,
    CommandeService,
    DetailService,
    HttpRequest,
    ImageService,
    FiltreService,
    TelechargementService,
    AdministrationService,
    LangueService,
    TemplateService,
    SelectionMenuService,
    {provide: MAT_DATE_LOCALE, useValue: 'fr-FR'},
  ],
  bootstrap: [AppComponent]
})

export class AppModule { }

// required for AOT compilation
  export function HttpLoaderFactory(http: HttpClient) {
    return new TranslateHttpLoader(http);
  }
