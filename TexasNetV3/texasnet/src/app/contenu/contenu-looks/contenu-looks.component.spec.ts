import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ContenuLooksComponent } from './contenu-looks.component';

describe('ContenuLooksComponent', () => {
  let component: ContenuLooksComponent;
  let fixture: ComponentFixture<ContenuLooksComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ContenuLooksComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ContenuLooksComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
