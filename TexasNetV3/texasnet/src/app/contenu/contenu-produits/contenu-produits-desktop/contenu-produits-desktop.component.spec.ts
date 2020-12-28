import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuProduitsDesktopComponent } from './contenu-produits-desktop.component';

describe('ContenuProduitsDesktopComponent', () => {
  let component: ContenuProduitsDesktopComponent;
  let fixture: ComponentFixture<ContenuProduitsDesktopComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuProduitsDesktopComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuProduitsDesktopComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
