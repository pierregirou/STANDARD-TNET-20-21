import { DetailProduit } from './detailProduit.model';

export class CategorieProduits{
    constructor(
        public n_id:number,
        public s_saison:string,
        public s_libelle:string,
        public s_libelle2:string,
        public s_refproduit:string,
        public s_codeMarque:string,
        public s_codeTheme:string,
        public s_famille:string,
        public s_sousFamille:string,
        public s_modele:string,
        public s_ligne:string,
        public s_texteLibre:string
    ){}
}
